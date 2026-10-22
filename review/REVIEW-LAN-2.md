# REVIEW LẦN 2 — Đồ án Quản lý phòng trọ

**Ngày review:** 22/10/2026  
**Tiêu chí đối chiếu:** `tieuchi.docx`, mục 3, 6, 7, 8.  
**Phạm vi:** Đọc lại toàn bộ `review/BAO-CAO-REVIEW.md`; khảo sát source, cấu hình, migrations, seeder/factory, routes, controllers, models, views, tests, README và artifact trong repository; truy vấn metadata/counts trên MySQL cấu hình ở chế độ read-only; GET một số route; chạy frontend build vào thư mục tạm và chạy PHPUnit trên cấu hình test riêng. Không sửa source hoặc dữ liệu DB mẫu.

## 1. Tóm tắt và điều chỉnh so với review trước

Review trước chỉ thấy `database/database.sqlite` và kết luận thiếu migration schema nghiệp vụ, nên diễn đạt quá mạnh rằng không có database chạy được. Lần này `.env` xác nhận ứng dụng trỏ tới MySQL `quanlyphongtro`; MariaDB tại `127.0.0.1:3306` truy cập được. Truy vấn read-only xác nhận database hiện có đủ sáu bảng nghiệp vụ (`properties`, `rooms`, `tenants`, `contracts`, `invoices`, `users`) và dữ liệu mẫu: 3 khu trọ, 9 phòng, 6 người thuê, 6 hợp đồng, 6 hóa đơn, 4 tài khoản. Các quan hệ FK và một số unique/index phục vụ toàn vẹn dữ liệu cũng đang có. Vì vậy **không kết luận chức năng hiện tại hỏng chỉ do thiếu migration tạo bảng**.

File `database/database.sqlite` không phải DB nghiệp vụ mà `.env` đang sử dụng; nó chỉ có bảng scaffold/framework (users/cache/jobs/sessions...). Không tìm thấy file SQL export trong project. DB MySQL hoạt động tại môi trường này nhưng không được đóng gói trong repository. Metadata cũng cho thấy DB đang chạy không có bảng `migrations`, vì vậy chưa chứng minh được DB mẫu được tạo từ migrations đang có.

Đã xác nhận một phần runtime: `GET /login` trả HTTP 200; `GET /dashboard`, `/invoices`, `/properties/1/rooms` khi chưa đăng nhập trả HTTP 302; `GET /` trả HTTP 404. Đây chưa phải kiểm thử thao tác có đăng nhập.

Frontend build thành công bằng Vite 8.3.1, output đặt trong thư mục tạm. PHPUnit dùng PHP 8.3.30 và SQLite `:memory:` theo `phpunit.xml`: **25 tests, 1 passed, 1 failed, 23 errors, 2 assertions**. Test fail là `ExampleTest`, kỳ vọng `/` 200 nhưng nhận 404. Các lỗi feature tiêu biểu dừng ở migration `add_property_id_to_rooms_table` vì SQLite test chưa có bảng `rooms`. PHPUnit không truy cập DB MySQL mẫu.

## 2. Tiêu chí chấm và trạng thái

Mục 8 của tài liệu nêu 5 tiêu chí, tổng trọng số 100%. File không kèm Rubric 2 đầy đủ 5 mức, vì vậy báo cáo không tự quy trạng thái thành điểm.

| Tiêu chí chính thức | Tối đa | Trạng thái | Bằng chứng hiện có | Còn thiếu / cách hoàn thiện tối thiểu |
|---|---:|---|---|---|
| Hình thức báo cáo (CLO1) | 10 | Chưa đủ bằng chứng | Không thấy báo cáo đồ án `.docx`/`.pdf` trong repository; file `review/BAO-CAO-REVIEW.md` là báo cáo review, không thay thế báo cáo nộp. | Đưa bản báo cáo nộp vào workspace để kiểm tra A4, Times New Roman 13, giãn dòng 1.5, tối thiểu 10 trang nội dung, số trang, caption/tham chiếu và chính tả. |
| Nội dung / chất lượng báo cáo (CLO5) | 10 | Chưa đủ bằng chứng | Chưa tìm thấy ERD, Use Case, báo cáo chức năng, ảnh kết quả, bảng phân công hoặc tài liệu tham khảo. | Nộp báo cáo với đầy đủ cấu trúc Mục 6.1; bổ sung ERD/Use Case và ảnh theo role. |
| Giao diện phần mềm (CLO2) | 30 | Đạt một phần | Có Blade views cho login, dashboard, properties, rooms, tenants, contracts, invoices, users; `GET /login` thực tế trả 200. Các form có CSRF, một số có `@error`; danh sách có flash. | Chưa walkthrough bằng tài khoản admin/manager và chưa đánh giá trực quan/responsive toàn màn hình. `GET /` trả 404; thiếu view show của contract/invoice trong cây source; khu quản trị chưa gom nhất quán dưới `/admin`. Chụp ảnh và kiểm tra màn hình thật sau khi xử lý các lỗi này. |
| Sản phẩm phần mềm (chức năng) (CLO4) | 40 | Đạt một phần | DB MySQL thật tại môi trường có schema và data mẫu; code có Eloquent, quan hệ, CRUD, auth, hai role/middleware, validation, flash, tìm kiếm. Build frontend thành công. | PHPUnit lỗi do bộ migration/test SQLite không dựng được schema. Chưa xác minh luồng CRUD/auth thực tế. Có lỗi validation cập nhật tài khoản và route/view invoice/contract cần xử lý. Không có gói DB tái tạo trên máy khác. |
| Tham gia thực hiện & tương tác nhóm (CLO3) | 10 | Chưa đủ bằng chứng | Không thấy danh sách thành viên, phân công, lịch sử Git hay bằng chứng trao đổi trong hồ sơ workspace. | Bổ sung bảng thành viên/nhiệm vụ/mức hoàn thành và bằng chứng đóng góp phù hợp. Phần này không thể kết luận chỉ từ source snapshot. |

### Điểm số

Tài liệu chỉ cung cấp trọng số và mô tả mức tối đa 8.5–10; thang quy đổi 5 mức được dẫn sang Rubric 2 nhưng không có trong file. Vì vậy chưa có cơ sở để tính điểm dự kiến chính xác. Trọng số chính thức là 10 + 10 + 30 + 40 + 10 = **100%**; không gán điểm tự đặt cho từng trạng thái.

## 3. Đối chiếu yêu cầu bắt buộc và dữ liệu thực tế

| Yêu cầu | Trạng thái | Bằng chứng và phần còn thiếu |
|---|---|---|
| Laravel/PHP và công nghệ giao diện | Đạt về cấu hình; xác minh một phần khi chạy | `composer.json` yêu cầu PHP `^8.3`, Laravel `^13.17`; runtime PHP được tìm thấy ở Laragon là 8.3.30; view dùng Blade, tài nguyên frontend build thành công với Vite. Composer CLI không có trong PATH hiện tại. |
| MySQL + Eloquent + migrations + Seeder/Factory | Đạt một phần | `.env` trỏ MySQL; DB cấu hình truy cập được, có 6 bảng nghiệp vụ, 65 cột, khóa ngoại/index và data mẫu. Model quan hệ ở `app/Models`. Nhưng source migrations không tạo schema nghiệp vụ đầy đủ, không có SQL dump; database thật không có bảng migrations; factory/seeder không khớp bảng users đang chạy (xem lỗi bên dưới). Cần artifact dựng lại trên máy khác. |
| Ít nhất 2 bảng quan hệ có CRUD | Có đủ xử lý trong source; chưa xác minh CRUD end-to-end | Controllers `RoomController`, `TenantController`, `ContractController`, `InvoiceController`, `PropertyController` triển khai store/index/edit/update/destroy; models có `hasMany`/`belongsTo`. Các quan hệ FK thật thấy trong MySQL. Chưa có kiểm thử request đăng nhập và thao tác thực tế. |
| Đăng nhập/đăng xuất và tối thiểu 2 role; bảo vệ bằng Middleware | Có trong source; xác minh một phần runtime | `AuthController.php:15-72`; `AdminMiddleware.php:11-25`; `PropertyAccess.php:11-35`; route nhóm tại `routes/web.php:23-98`. Guest GET vào `/dashboard` được chuyển hướng 302. Chưa thử credential hợp lệ/role chéo vì không có tài khoản thử do người dùng cung cấp. Tài liệu mô tả khu admin tại `/admin`; hiện route CRUD property nằm ở `/properties`, còn `/admin/users` chỉ quản lý user. |
| Tìm kiếm + phân trang + giữ keyword | Đạt yêu cầu tối thiểu trong source | `PropertyController.php:16-39` tìm theo keyword/status, `paginate(10)->withQueryString()`. Đây là một danh sách chính đáp ứng phần tìm kiếm, phân trang và giữ query. Chưa xác minh khi đăng nhập. Danh sách room/tenant/contract/invoice dùng `get()` nên chưa phân trang. |
| Validation khi nhập form, thông báo lỗi rõ | Có trong source; một lỗi cập nhật cần sửa | `validate()` được dùng trong controllers; nhiều form có `@error`. Riêng `UserController.php:106-113` khai báo rule username dạng chuỗi `Rule::unique(...)->ignore(...)` thay vì object `Rule::unique(...)`; có khả năng gây lỗi “validation rule không tồn tại” khi cập nhật user. Chưa POST form thật. |
| Flash message sau thêm/sửa/xóa | Có trong source | `with('success', ...)` có trong các controller nghiệp vụ và một số view index hiển thị `session('success')`; property/user list cũng có `error`. Chưa xác minh từng thao tác runtime. |
| Database mẫu hiện có và toàn vẹn quan hệ | Đạt cho DB đang chạy; chưa đóng gói | Metadata read-only xác nhận 3 properties, 9 rooms, 6 tenants, 6 contracts, 6 invoices, 4 users. Có FK rooms/tenants/contracts/invoices/users và unique index room theo property+room number, invoice theo contract+month. Mọi nhận xét này chỉ áp dụng database cục bộ cấu hình hiện tại. Không có SQL export trong repository. |
| Hướng dẫn cài/chạy cho máy khác | Chưa đạt | `README.md` là README mặc định Laravel; không nói về DB sample, tài khoản demo, import SQL, migrate/seed, URL chạy hay cách đăng nhập. Cần README dự án. |
| Test tự động | Chưa đạt ở trạng thái hiện tại | PHPUnit chạy được nhưng 1 fail + 23 errors. Test suite đang scaffold Laravel/Breeze, không có test nghiệp vụ cho property/room/tenant/contract/invoice hoặc ranh giới role/property. |

## 4. Schema MySQL đã kiểm tra (read-only)

Schema đã đọc qua kết nối tới DB ghi trong `.env`; truy vấn được đặt `SET SESSION TRANSACTION READ ONLY`. Không đọc nội dung cá nhân của các bản ghi, không insert/update/delete và không chạy migration.

| Bảng | Cột tiêu biểu / quan hệ | Số bản ghi tại thời điểm kiểm tra |
|---|---|---:|
| `properties` | id, name, address, phone, description, status | 3 |
| `rooms` | property_id FK, room_number, price, area, status | 9 |
| `tenants` | property_id FK, full_name, phone, cccd, address, thông tin phụ | 6 |
| `contracts` | property_id, room_id, tenant_id FK; ngày, tiền thuê, đặt cọc, status | 6 |
| `invoices` | contract_id FK; tháng, điện, nước, phí, other_fee, total, status | 6 |
| `users` | property_id FK; username, name, email, password, role | 4 |

DB thực có cấu trúc bổ sung/ràng buộc phong phú hơn các migration hiện có. Ví dụ contracts có FK bảo đảm `room_id` và `tenant_id` cùng thuộc property; invoices có unique `(contract_id, month)`. Đây là bằng chứng schema đang chạy tốt hơn những gì file migration tự mô tả, nhưng không giúp người nhận source dựng lại DB nếu thiếu export/migration tương ứng.

## 5. Kiểm tra thực tế đã chạy

| Kiểm tra | Kết quả | Giới hạn |
|---|---|---|
| `GET http://127.0.0.1:8000/login` | HTTP 200 | Xác nhận trang login được phục vụ, không xác nhận đăng nhập. |
| `GET /dashboard`, `/invoices`, `/properties/1/rooms` khi guest | HTTP 302 | Phù hợp với nhóm route yêu cầu đăng nhập; không kiểm tra quyền sau đăng nhập. |
| `GET /` | HTTP 404 | Test `ExampleTest` cũng thất bại vì kỳ vọng 200. Không có route root trong `routes/web.php`. |
| `npm --prefix <project> run build -- --outDir <thư mục tạm>` | Thành công, Vite 8.3.1; manifest/CSS/JS tạo trong temp | Không ghi `public/build`; không kiểm tra backend Blade sau đăng nhập. |
| `php artisan test --do-not-cache-result` với PHP 8.3.30 | 25 tests; 1 passed, 1 failed, 23 errors, 2 assertions | PHPUnit config dùng SQLite `:memory:`. Lỗi migration tiêu biểu: `no such table: rooms` tại migration thêm `property_id`; test fail `/` trả 404 thay vì 200. Không dùng DB MySQL mẫu. |
| MySQL schema/counts | Đọc thành công, read-only | Chỉ schema/counts/FK/index. Không thử CRUD để tránh sửa dữ liệu mẫu. |

## 6. Lỗi và rủi ro theo mức độ

### High

1. **Không tái tạo được database nộp từ repository hiện tại.** DB lục bộ đang chạy có đủ bảng nhưng không có `migrations`; không có file SQL trong repository; migration users không có `username`/`role`, migration property giả định `role` đã tồn tại, và migration room/tenant/contract alter các bảng chưa được tạo trong source. Trên DB SQLite test sạch, migrate dừng ở `rooms` chưa tồn tại. **Tác động:** demo cục bộ có thể chạy, nhưng người nhận source không có cách dựng cùng schema/data một cách lặp lại như yêu cầu Mục 7. Tối thiểu: nộp sanitized SQL export được tạo từ schema/data demo đang chạy, hoặc đồng bộ đầy đủ migrations + seeder.
2. **Seeder/factory không tương thích schema users hiện tại.** `UserFactory.php:25-34` chỉ tạo `name`, `email`, `email_verified_at`, `password`, `remember_token`; DB MySQL hiện yêu cầu `username` và `role` NOT NULL. `DatabaseSeeder.php:20-23` gọi factory không thêm hai trường đó. Seed trên DB hiện tại có nguy cơ lỗi. Tối thiểu: bổ sung factory/seed theo admin/manager và dữ liệu quan hệ demo, rồi thử trên DB test riêng.
3. **Sửa tài khoản có rule username sai.** `UserController.php:106-113` truyền biểu thức Rule::unique dưới dạng chuỗi. Tối thiểu đổi về rule object và test update giữ username/cập nhật username trùng.

### Medium

1. **Route `/` không phục vụ trang bắt đầu.** Đã xác minh HTTP 404; test scaffold đòi 200. Tối thiểu redirect `/` sang `/login` hoặc dashboard phù hợp.
2. **Hai trang show thiếu view trong project.** `ContractController.php:130-145` render `contracts.show`; `InvoiceController.php:103-120` render `invoices.show`; cây `resources/views` có index/create/edit nhưng không có hai file show này. Resource contract/invoice vẫn bật action show (`routes/web.php:96,98`). Tối thiểu bổ sung view hoặc bỏ action show nếu không cần.
3. **Resource invoices cấp gốc không có `property` trong URL.** `routes/web.php:39` đăng ký resource `/invoices`; mọi action `InvoiceController` nhận `Property $property` (bắt đầu ở dòng 12). Ngoài resource lồng `/properties/{property}/invoices` ở dòng 98, route root không cung cấp property. GET guest bị auth redirect nên chưa thực thi controller; đây là rủi ro source-level. Tối thiểu bỏ route root nếu chỉ quản lý theo property.
4. **Chỉ một danh sách có search+pagination đầy đủ.** Property list đáp ứng yêu cầu tối thiểu. Room, tenant, contract, invoice đang dùng `get()`; nên thêm pagination nếu các danh sách này dự kiến dùng với dữ liệu tăng.
5. **Bộ test không chạy được trên cấu hình SQLite khai báo.** 23 errors do migration/setup (lỗi tiêu biểu `rooms` chưa tồn tại); 1 test root path fail. Test hiện không chứng minh chất lượng CRUD cốt lõi. Tối thiểu cung cấp schema test độc lập (migration SQLite-compatible) và test các luồng cốt lõi, không trỏ test vào DB demo.
6. **Tổng hóa đơn cần xác nhận nghiệp vụ.** `InvoiceController.php` tính `total` bằng điện + nước + service_fee; trong schema có `other_fee` và hợp đồng có `monthly_rent`. Nếu hóa đơn tháng của đề tài gồm tiền phòng/phí khác, trường hợp đó chưa được tính/nhập; xác nhận quy tắc tính trước khi sửa.
7. **Admin area chưa tương ứng rõ với `/admin` trong tiêu chí kỹ thuật.** User management ở `/admin/users`, nhưng property CRUD ở `/properties` và dashboard ở `/dashboard`. Có middleware admin; cần xác nhận với giảng viên URL phân khu này có chấp nhận hay không. Cách bổ sung nhỏ nhất là một `/admin` entry/redirect và điều hướng nhất quán, không cần viết lại CRUD.

### Chưa đủ bằng chứng (không xem là lỗi đã xác nhận)

- Đăng nhập hợp lệ, logout, vai trò admin/manager, chặn truy cập chéo property và các luồng CRUD khi người dùng đã đăng nhập.
- Đánh giá màu sắc/bố cục/UX và responsive trên màn hình nhỏ; mới có login GET và viewport meta ở một số view.
- Bài báo cáo, ERD, Use Case, ảnh có caption, tài liệu tham khảo, phân công và mức đóng góp nhóm.
- Slide nếu lớp có yêu cầu bảo vệ.
- Quy tắc tính tiền hóa đơn được giảng viên/nhóm xác nhận.

## 7. Ưu tiên bổ sung nhỏ nhất

1. Sửa lỗi update user, route root 404, resource invoices root dư và hai action show thiếu view.
2. Hoàn thiện artifact tái tạo DB: ưu tiên export SQL sạch từ schema/data demo (xóa/anonymize dữ liệu cá nhân trước khi nộp); ghi rõ cách import. Nếu chọn migrations thì phải bao quát schema hiện có và seeder phải khớp schema.
3. Sửa seeder/factory để dựng được role admin/manager và dữ liệu mẫu liên quan trên DB thử nghiệm riêng.
4. Viết README ngắn theo đúng project: PHP/Composer/Node, import DB, `.env`, key, chạy frontend/backend, URL và tài khoản demo.
5. Thêm test nhỏ cho login/role, property scope, CRUD room/tenant/contract/invoice và quy tắc tính invoice; giữ test DB tách biệt DB demo. Sửa cấu hình SQLite/migration để PHPUnit có thể chạy.
6. Hoàn tất bộ hồ sơ báo cáo: DOCX/PDF theo mục 6.1/6.2, ERD, Use Case, screenshot theo role, phân công, tài liệu tham khảo; xin Rubric 2 đầy đủ để nhóm tự đánh giá theo thang chính thức.
7. Walkthrough trực tiếp bằng account demo trên desktop/mobile và ghi lại kết quả. Không cần viết lại màn hình/CRUD đã hoạt động tốt.

## 8. Danh sách cuối theo yêu cầu

### A. Đã đạt hoặc đã có, không cần viết lại

- Ứng dụng Laravel/Blade; frontend build thành công.
- DB MySQL/MariaDB hiện tại có đủ bảng và dữ liệu mẫu; có FK/unique/index hợp lý. **Không cần sửa schema đang chạy chỉ vì migration thiếu.**
- Models/relationships cho property-room-tenant-contract-invoice-user; các controller có CRUD và validation trong source.
- Hai role admin/manager và middleware auth/admin/property access có trong source.
- Property list có tìm kiếm, phân trang 10 dòng và giữ query string; đáp ứng yêu cầu tối thiểu trong source.
- Flash success/error và CSRF trong các form/list đã khảo sát.
- Trang `/login` trả HTTP 200.

### B. Có chức năng nhưng cần bổ sung hoặc hoàn thiện

- Cập nhật tài khoản: sửa rule unique username; xác nhận chỉnh sửa admin không bắt buộc gán property.
- Trang bắt đầu `/` hiện 404; thêm redirect đơn giản.
- Route root invoices và các route show contract/invoice cần khớp với property scope và view.
- Room/tenant/contract/invoice có search nhưng không paginate; ưu tiên hoàn thiện khi phù hợp dữ liệu demo.
- Quy tắc tổng tiền hóa đơn cần đối chiếu yêu cầu nghiệp vụ; không tự đổi nếu nhóm chưa xác nhận.
- Khu admin có middleware nhưng URL chưa nhất quán với `/admin` trong yêu cầu.
- Test đang tồn tại nhưng scaffold/config hiện lỗi; cần sửa cho test cốt lõi chạy được.

### C. Còn thiếu, cần làm trước khi nộp

- File báo cáo DOCX/PDF, ERD, UML Use Case, screenshot có chú thích/tham chiếu, tài liệu tham khảo.
- Bảng thành viên, MSSV, phân công và mức hoàn thành; bằng chứng tương tác/đóng góp.
- README hướng dẫn trên máy khác và tài khoản demo.
- SQL export (hoặc migrations + seeder hoàn chỉnh) để dựng lại database. DB có trên máy này chưa thay thế artifact nộp; cần export đã ẩn danh dữ liệu cá nhân.
- Rubric 2 đầy đủ từ đề cương nếu muốn tự ước lượng điểm theo đúng thang 5 mức.

## Phụ lục — file/cụm file đã kiểm tra

`tieuchi.docx`, `review/BAO-CAO-REVIEW.md`, `README.md`, `.env` (chỉ đọc key/config cần thiết, không đưa credential vào báo cáo), `.env.example`, `composer.json`, `package.json`, `phpunit.xml`, `config/database.php`, `routes/web.php`, `bootstrap/app.php`, toàn bộ controllers/models/middleware, migrations/factory/seeder, Blade views, `tests/`, `database/database.sqlite` và metadata read-only của MySQL `quanlyphongtro`.
