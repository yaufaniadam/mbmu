<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $jadwal_produksi_id
 * @property int $sekolah_id
 * @property int|null $user_id
 * @property int $jumlah_porsi_besar
 * @property int $jumlah_porsi_kecil
 * @property string $status_pengantaran
 * @property string|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $courier
 * @property-read \App\Models\ProductionSchedule $productionSchedule
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereJadwalProduksiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereJumlahPorsiBesar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereJumlahPorsiKecil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereStatusPengantaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Distribution whereUserId($value)
 */
	class Distribution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $jadwal_produksi_id
 * @property int $user_id User Staf Gizi
 * @property string|null $checklist_data
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductionSchedule $productionSchedule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereChecklistData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereJadwalProduksiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodVerification whereUserId($value)
 */
	class FoodVerification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_lembaga
 * @property string $alamat_lembaga
 * @property int|null $pimpinan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $pimpinan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sppg> $sppgs
 * @property-read int|null $sppgs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul whereAlamatLembaga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul whereNamaLembaga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul wherePimpinanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LembagaPengusul whereUpdatedAt($value)
 */
	class LembagaPengusul extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sppg_id
 * @property string $tanggal
 * @property string $menu_hari_ini
 * @property int|null $jumlah
 * @property string $status
 * @property string|null $catatan_ditolak
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Distribution> $distributions
 * @property-read int|null $distributions_count
 * @property-read bool $is_fully_delivered
 * @property-read mixed $total_porsi_besar
 * @property-read float|int $total_porsi_kecil
 * @property-read \App\Models\Sppg $sppg
 * @property-read \App\Models\FoodVerification|null $verification
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereCatatanDitolak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereMenuHariIni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereSppgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionSchedule whereUpdatedAt($value)
 */
	class ProductionSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sppg_id
 * @property string $nama_sekolah
 * @property string $alamat
 * @property string|null $province_code
 * @property string|null $city_code
 * @property string|null $district_code
 * @property string|null $village_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Distribution> $distributions
 * @property-read int|null $distributions_count
 * @property-read \App\Models\Sppg $sppg
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereDistrictCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereNamaSekolah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSppgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereVillageCode($value)
 */
	class School extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $kepala_sppg_id
 * @property int|null $lembaga_pengusul_id
 * @property string $nama_sppg
 * @property string $kode_sppg
 * @property string|null $nama_bank
 * @property string|null $nomor_va
 * @property string $alamat
 * @property string|null $province_code
 * @property string|null $city_code
 * @property string|null $district_code
 * @property string|null $village_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $kepalaSppg
 * @property-read \App\Models\LembagaPengusul|null $lembagaPengusul
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductionSchedule> $productionSchedules
 * @property-read int|null $production_schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\School> $schools
 * @property-read int|null $schools_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $staff
 * @property-read int|null $staff_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereDistrictCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereKepalaSppgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereKodeSppg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereLembagaPengusulId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereNamaBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereNamaSppg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereNomorVa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sppg whereVillageCode($value)
 */
	class Sppg extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sppg_id
 * @property int $user_id
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole whereSppgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SppgUserRole whereUserId($value)
 */
	class SppgUserRole extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $telepon
 * @property string|null $alamat
 * @property string|null $nik
 * @property string|null $photo_path
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LembagaPengusul|null $lembagaDipimpin
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Sppg|null $sppgDiKepalai
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sppg> $unitTugas
 * @property-read int|null $unit_tugas_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sppg_id
 * @property string $nama_relawan
 * @property string $posisi
 * @property string|null $kontak
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereNamaRelawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer wherePosisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereSppgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereUpdatedAt($value)
 */
	class Volunteer extends \Eloquent {}
}

