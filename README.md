# Quản lý phòng trọ

Ứng dụng web Laravel quản lý khu trọ, phòng, người thuê, hợp đồng, hóa đơn và tài khoản quản lý.

## Công nghệ

- PHP 8.3 trở lên và Laravel 13.
- MySQL/MariaDB cho môi trường chạy; Eloquent ORM và Laravel migrations.
- Blade, Bootstrap 5.3 và Vite.

## Vai trò

- **Admin** là quản trị viên toàn hệ thống: quản lý khu trọ và tài khoản tại khu vực `/admin`.
- **Manager** là vai trò `User` cụ thể hóa theo bài toán quản lý phòng trọ: nhân viên vận hành khu được phân công, có thể quản lý phòng/người thuê/hợp đồng/hóa đơn trong phạm vi property đó. Manager không phải tài khoản khách công khai và không có quyền xem dữ liệu khu khác.
- Nếu giảng viên diễn giải role `User` ở Mục 3.2 bắt buộc là khách hàng công khai chỉ đọc dữ liệu, cần xác nhận với giảng viên vì mô hình hiện tại dùng manager nội bộ phù hợp nghiệp vụ nhà trọ.

## Yêu cầu cài đặt

- PHP `^8.3` cùng các extension Laravel cần thiết, Composer 2.x.
- Node.js và npm.
- MySQL 8+ hoặc MariaDB tương thích; database riêng cho ứng dụng.

## Cài đặt database mới

Chỉ chạy quy trình này trên database local/test mới, rỗng. Không chạy `migrate:fresh` trên database đang có dữ liệu.

```powershell
composer install
Copy-Item .env.example .env
```

Điền `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`, tạo database rỗng trên MySQL/MariaDB, sau đó:

```powershell
php artisan key:generate
php artisan migrate --force
php artisan db:seed
npm install
npm run build
```

Seeder demo chỉ chạy khi `APP_ENV=local` hoặc `testing`. Có thể đặt `DEMO_ACCOUNT_PASSWORD` trước khi seed; mặc định local là `ChangeMe123!`. Seeder có thể chạy lại mà không tạo thêm bản ghi trùng cho bộ demo.

Tài khoản demo được seed:

| Vai trò | Username | Mật khẩu mặc định local |
|---|---|---|
| Admin | `demo_admin` | `ChangeMe123!` |
| Manager | `demo_manager` | `ChangeMe123!` |

Đây là thông tin chỉ dùng ở môi trường demo local. Không dùng mật khẩu mặc định cho tài khoản thật.

## Chạy ứng dụng

```powershell
php artisan serve
```

Mở URL được Artisan in ra, đăng nhập bằng tài khoản demo ở trên. Vite development server (nếu cần hot reload) chạy ở terminal khác:

```powershell
npm run dev
```

## Kiểm thử

```powershell
php artisan test
```

Tests tự động dùng SQLite in-memory theo `phpunit.xml`, không dùng database demo. Các test scaffold Breeze cho tính năng không được ứng dụng triển khai (đăng ký, xác nhận/đặt lại mật khẩu, email verification, profile) được loại khỏi suite mặc định; chạy chúng riêng sẽ không đại diện cho tiêu chí nghiệp vụ hiện có.

## Cấu trúc chức năng

- `app/Models`: quan hệ Eloquent.
- `app/Http/Controllers`: đăng nhập, dashboard và CRUD.
- `app/Http/Middleware`: giới hạn quyền admin và property của manager.
- `routes/web.php`: route login, khu `/admin` và các module theo property.
- `database/migrations`, `database/factories`, `database/seeders`: schema và dữ liệu local/test.
- `tests/Feature`: kiểm thử HTTP, validation, phân quyền và nghiệp vụ hóa đơn.
