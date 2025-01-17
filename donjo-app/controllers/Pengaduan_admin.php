<?php

/*
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2024 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package   OpenSID
 * @author    Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2024 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license   http://www.gnu.org/licenses/gpl.html GPL V3
 * @link      https://github.com/OpenSID/OpenSID
 *
 */

use App\Enums\StatusPengaduanEnum;
use App\Models\Pengaduan;

defined('BASEPATH') || exit('No direct script access allowed');

class Pengaduan_admin extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->modul_ini = 'pengaduan';
        $this->load->model('pengaduan_model');
    }

    public function index()
    {
        $data = $this->widget();

        return view('admin.pengaduan_warga.index', $data);
    }

    protected function widget()
    {
        return [
            'allstatus'   => Pengaduan::pengaduan()->count(),
            'status1'     => Pengaduan::pengaduan(1)->count(),
            'status2'     => Pengaduan::pengaduan(2)->count(),
            'status3'     => Pengaduan::pengaduan(3)->count(),
            'm_allstatus' => Pengaduan::bulanan()->count(),
            'm_status1'   => Pengaduan::bulanan(1)->count(),
            'm_status2'   => Pengaduan::bulanan(2)->count(),
            'm_status3'   => Pengaduan::bulanan(3)->count(),
        ];
    }

    public function datatables()
    {
        if ($this->input->is_ajax_request()) {
            $status = $this->input->get('status');

            return datatables()->of(Pengaduan::pengaduan()->filter($status))
                ->addColumn('ceklist', static function ($row) {
                    if (can('h')) {
                        return '<input type="checkbox" name="id_cb[]" value="' . $row->id . '"/>';
                    }
                })
                ->addIndexColumn()
                ->addColumn('aksi', static function ($row) {
                    $aksi = '';

                    if (can('u')) {
                        $aksi .= '<a href="' . route('pengaduan_admin.form', $row->id) . '" class="btn btn-warning btn-sm"  title="Tanggapi Pengaduan"><i class="fa fa-mail-forward"></i></a> ';
                    }

                    if (can('u')) {
                        $aksi .= '<a href="' . route('pengaduan_admin.detail', $row->id) . '" class="btn btn-info btn-sm"  title="Lihat Detail"><i class="fa fa-eye"></i></a> ';
                    }

                    if (can('h')) {
                        $aksi .= '<a href="#" data-href="' . route('pengaduan_admin.delete', $row->id) . '" class="btn bg-maroon btn-sm"  title="Hapus Data" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a> ';
                    }

                    return $aksi;
                })
                ->editColumn('status', static function ($row) {
                    if ($row->status == StatusPengaduanEnum::MENUNGGU_DIPROSES) {
                        $tipe = 'danger';
                    } elseif ($row->status == StatusPengaduanEnum::SEDANG_DIPROSES) {
                        $tipe = 'info';
                    } elseif ($row->status == StatusPengaduanEnum::SELESAI_DIPROSES) {
                        $tipe = 'success';
                    }

                    return '<span class="label label-' . $tipe . '">' . ucwords(StatusPengaduanEnum::valueOf($row->status)) . ' </span>';
                })
                ->rawColumns(['ceklist', 'aksi', 'status'])
                ->make();
        }

        return show_404();
    }

    public function form($id = '')
    {
        $this->redirect_hak_akses('u');

        if ($id) {
            $action          = 'Tanggapi Pengaduan';
            $form_action     = route('pengaduan_admin.kirim', $id);
            $pengaduan_warga = Pengaduan::findOrFail($id);
        }

        return view('admin.pengaduan_warga.form', compact('action', 'form_action', 'pengaduan_warga'));
    }

    public function kirim($id)
    {
        $this->redirect_hak_akses('u');

        try {
            $pengaduan = Pengaduan::findOrFail($id);
            $pengaduan->update(['status' => $this->request['status']]);

            Pengaduan::where('id_pengaduan', $id)->update(['status' => $this->request['status']]);

            Pengaduan::create([
                'id_pengaduan' => $id,
                'nama'         => $this->session->nama,
                'isi'          => bersihkan_xss($this->request['isi']),
                'status'       => $this->request['status'],
                'ip_address'   => $this->input->ip_address() ?? '',
            ]);

            redirect_with('success', 'Berhasil Ditanggapi');
        } catch (Exception $e) {
            log_message('error', $e);
        }

        redirect_with('error', 'Gagal Ditanggapi');
    }

    public function detail($id = '')
    {
        $this->redirect_hak_akses('u');

        if ($id) {
            $action          = 'Detail Pengaduan';
            $pengaduan_warga = Pengaduan::findOrFail($id);
            $tanggapan       = Pengaduan::where('id_pengaduan', $id)->get();
        }

        return view('admin.pengaduan_warga.detail', compact('action', 'pengaduan_warga', 'tanggapan'));
    }

    public function delete($id = null)
    {
        $this->redirect_hak_akses('h');

        try {
            Pengaduan::destroy($id ?? $this->request['id_cb']);

            // Hapus komentar
            if ($id) {
                $this->request['id_cb'] = [$id];
            }
            Pengaduan::whereIn('id_pengaduan', $this->request['id_cb'])->delete();

            redirect_with('success', 'Berhasil Hapus Data');
        } catch (Exception $e) {
            log_message('error', $e);
        }

        redirect_with('error', 'Gagal Hapus Data');
    }
}
