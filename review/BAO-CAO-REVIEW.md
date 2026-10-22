# Báo cáo review đồ án Quản lý phòng trọ

**Ngày review:** 22/10/2026  
**Phạm vi:** Source, cấu hình, migration, view và tài liệu hiện có trong workspace. Đây là đánh giá tĩnh; chưa xác minh ứng dụng bằng cách chạy.

## 1. Tổng quan dự án

Đây là ứng dụng web quản lý nhà trọ xây trên Laravel 13, PHP yêu cầu `^8.3`, giao diện Blade, Vite, Tailwind và Alpine. Mô hình nghiệp vụ thể hiện qua mã gồm khu nhà trọ (property), phòng, người thuê, hợp đồng, hóa đơn và tài khoản. Có hai role `admin` và `manager`; admin quản lý khu/tài khoản, manager làm việc trong property được phân công. Các điểm vào chính nằm ở `routes/web.php`, controller trong `app/Http/Controllers`, model trong `app/Models`, middleware và view Blade.

README (mặc định Laravel) chưa chỉ cách cài/chạy ứng dụng này. `.env.example` mặc định MySQL, session/cache/queue dùng database. Có `database/database.sqlite` trong workspace nhưng không có cơ sở để kết luận đó là DB có thể dựng lại hoặc khớp schema hiện tại. Trong thư mục migration chỉ thấy migration users/cache/jobs, tạo properties và các migration thêm `property_id`; không thấy migration tạo bảng rooms, tenants, contracts hoặc invoices. Migration hợp đồng dùng cú pháp `UPDATE ... JOIN` kiểu MySQL.

`tests/` có các test mẫu Laravel/Breeze và hai test example; chưa thấy test nghiệp vụ cho property, phòng, người thuê, hợp đồng, hóa đơn hoặc phân quyền. Không tìm thấy trong file dự án các tài liệu báo cáo đồ án, ERD, Use Case, phân công nhóm, ảnh kết quả hoặc file export SQL. `tieuchi.docx` đọc được; rubric 2 đầy đủ 5 mức mà tài liệu dẫn chiếu không có trong file được cung cấp.

**Setup/thực thi:** đã thử `php -v` và `composer -V`; cả hai lệnh không khả dụng trong shell hiện tại. Vì vậy không chạy được PHPUnit, artisan hay kiểm tra migration/runtime. Không chạy `npm run build` vì thao tác build có thể cập nhật `public/build` và workspace hiện ở chế độ chỉ đọc. Không cài PHP/Composer/Boost vì đó là thay đổi môi trường cần được cho phép riêng.

## 2. Tiêu chí đối chiếu và điểm tối đa

Tài liệu mục 8 nêu đúng 5 tiêu chí sau. Trạng thái bên dưới đánh giá bằng chứng trong workspace; “Đạt một phần” không đồng nghĩa đã xác nhận chạy đúng.

| Tiêu chí theo tài liệu | Điểm tối đa | Yêu cầu nhỏ có thể kiểm chứng | Trạng thái | Bằng chứng / nhận xét |
|---|---:|---|---|---|
| Hình thức báo cáo (CLO1) | 10 | Báo cáo A4; Times New Roman 13; giãn dòng 1.5; tối thiểu 10 trang nội dung; đánh số trang, chương/mục rõ; sơ đồ/ảnh có caption và tham chiếu; trình bày nhất quán, không lỗi chính tả. | Chưa đủ bằng chứng | Không tìm thấy báo cáo `.docx`/`.pdf` trong repository. Các yêu cầu định dạng cần xem file báo cáo bản nộp. |
| Nội dung / chất lượng báo cáo (CLO5) | 10 | Bìa; mục lục; giới thiệu; ERD; Use Case; mô tả chức năng; công nghệ; cài đặt/sử dụng; kết quả theo role; phân công; hạn chế/hướng phát triển; tài liệu tham khảo. | Chưa đủ bằng chứng | Không tìm thấy báo cáo, ERD, Use Case hoặc tài liệu hướng dẫn riêng. README hiện là tài liệu mẫu Laravel, không mô tả sản phẩm. |
| Giao diện phần mềm (CLO2) | 30 | Bố cục, màu sắc, trải nghiệm phù hợp; đủ giao diện theo đề tài/role; phản hồi validation/flash; xem được trên màn hình nhỏ nếu tuyên bố responsive. | Đạt một phần | Có view Blade cho login/dashboard và CRUD của property, room, tenant, contract, invoice, user. Có flash message ở một số danh sách. Chưa chạy ứng dụng/đánh giá trực quan hoặc responsive; một số endpoint show tham chiếu view chưa thấy. |
| Sản phẩm phần mềm (chức năng) (CLO4) | 40 | Laravel; MySQL + Eloquent + migrations + seed/factory; ít nhất 2 bảng quan hệ CRUD; auth/logout + ít nhất 2 role bảo vệ bằng middleware; tìm kiếm + phân trang + giữ từ khóa; validation; flash; hoạt động đúng; có thể cài lại từ nguồn. | Đạt một phần | Có models quan hệ, controllers CRUD, auth, role middleware, validation, flash và tìm kiếm. Nhưng migration thiếu bảng nghiệp vụ nền; danh sách nghiệp vụ dùng `get()` thay vì paginate; route hóa đơn root lệch chữ ký; chưa chạy được để chứng minh hoạt động. Seeder chỉ tạo một user factory mặc định, không tạo dữ liệu nghiệp vụ/role mẫu. |
| Tham gia thực hiện & tương tác nhóm (CLO3) | 10 | Tương tác trong quá trình làm nhóm; đóng góp đồng đều; đối chiếu danh sách thành viên và nhiệm vụ. | Chưa đủ bằng chứng | Không thấy bảng phân công, danh sách thành viên, lịch sử Git hay bằng chứng trao đổi trong các file được khảo sát. Mục tiêu này không thể kết luận từ code snapshot đơn lẻ. |

### Hồ sơ và yêu cầu bổ sung

| Yêu cầu trong tài liệu | Trạng thái | Bằng chứng |
|---|---|---|
| Source đầy đủ; DB export hoặc migrations + seeder dựng lại; README máy khác; slide nếu có buổi bảo vệ | Chưa đạt với artifact hiện có / chưa đủ bằng chứng với slide | Không thấy export SQL; migration thiếu schema nghiệp vụ; README mặc định; không tìm thấy slide. Không có thông tin lớp có tổ chức bảo vệ hay không. |
| ERD, Use Case, mô tả đề tài/phạm vi, ảnh chức năng, phân công | Chưa đủ bằng chứng | Không tìm thấy các hồ sơ này trong cây file dự án. Có thể chúng nằm ngoài workspace. |
| Tìm kiếm + phân trang trên danh sách chính, giữ từ khóa khi chuyển trang | Đạt một phần | Property có `paginate(10)->withQueryString()` (`PropertyController.php:16-39`). Room/tenant/contract/invoice gọi `get()` (`RoomController.php:11-25`, `TenantController.php:11-28`, `ContractController.php:14-43`, `InvoiceController.php:12-47`); user có paginate nhưng không thấy search (`UserController.php:13-17`). |
| Role và route bảo vệ | Đạt một phần | Nhóm auth, admin và property access ở `routes/web.php:23-98`; middleware kiểm tra role/property. Chưa kiểm thử runtime. User không có luồng đăng ký công khai, nhưng tài liệu chỉ bắt buộc đăng nhập/role và user tùy đề tài; cần đối chiếu cách nhóm xác định user của bài toán. |
| Yêu cầu mở rộng | Chưa đủ bằng chứng | Không thấy tích hợp upload/storage, AJAX hay API ngoài từ code đã khảo sát. Responsive cần xem giao diện thực tế. Không cộng điểm mở rộng khi chưa có bằng chứng chạy được. |

## 3. Bằng chứng cấu trúc và hành vi

- Route auth/dashboard/admin/property: `routes/web.php:15-109`; alias middleware: `bootstrap/app.php:12-20`.
- Đăng nhập validate credential, gọi `Auth::attempt`, regenerate session và redirect theo role: `AuthController.php:15-58`; logout invalidate session/token: `AuthController.php:61-72`.
- Quyền admin: `AdminMiddleware.php:11-25`; property manager chỉ truy cập property được gán, admin bypass: `PropertyAccess.php:11-35`.
- Eloquent relationships: `Property.php:23-41`, `Room.php:21-28`, `Tenant.php:21-29`, `Contract.php:26-55`, `Invoice.php:37-40`; password hash cast ở `User.php:30-34`.
- Validation và kiểm tra cùng property trong các controller: rooms (`RoomController.php:33-106`), tenants (`TenantController.php:38-117`), contracts (`ContractController.php:67-307`), invoices (`InvoiceController.php:67-218`). Contracts có transaction, khóa bản ghi và đồng bộ trạng thái phòng (`ContractController.php:80-127`, `185-238`, `253-307`). Đây là bằng chứng logic có trong source, chưa thay thế kiểm thử.
- Tìm kiếm phòng/người thuê/hợp đồng/hóa đơn có query nhưng không phân trang. Riêng room/tenant `get()` trả mọi kết quả, ảnh hưởng tải khi dữ liệu tăng.
- `DatabaseSeeder.php:16-25` tạo `Test User` qua factory, chưa tạo dữ liệu property/room/tenant/contract/invoice hoặc account admin-manager có thể dùng để demo.
- `composer.json` khai báo Laravel `^13.17`, PHP `^8.3`; scripts `setup`, `dev`, `test` có nhưng `setup` gồm migrate và build (`composer.json:6-14`, `38-62`). README không giải thích biến môi trường, tài khoản mẫu, quy trình migrate/seed hay lệnh chạy.

## 4. Lỗi và rủi ro theo mức độ

### Critical

- **Không thể dựng lại database nghiệp vụ từ migration hiện có:** chỉ có migration tạo users/cache/jobs/properties; các migration sau gọi `Schema::table('rooms'|'tenants'|'contracts')` nhưng không có migration tạo các bảng đó, đồng thời không thấy migration tạo `invoices`. Trên database mới, migration sẽ thất bại khi đụng bảng chưa tồn tại; chức năng nghiệp vụ cũng không có schema được quản lý trong source. Bằng chứng: toàn bộ `database/migrations/`; các thao tác cụ thể `2006_07_03_081050_add_property_id_to_rooms_table.php:12`, `2006_07_03_081343_add_property_id_to_tenants_table.php:12`, `2006_07_03_081404_add_property_id_to_contracts_table.php:12`.

### High

- **Route resource hóa đơn cấp gốc không tương thích controller:** `routes/web.php:39` đăng ký `Route::resource('invoices', InvoiceController::class)` ngoài `properties/{property}`, trong khi `InvoiceController` yêu cầu `Property $property` trong index/create/store/show/edit/update/destroy (`InvoiceController.php:12` trở đi). Route root không cung cấp tham số property; khả năng cao request lỗi resolve tham số. Resource hóa đơn lồng property tiếp tục được đăng ký ở `routes/web.php:98`.
- **Chức năng show trỏ tới view chưa thấy trong repository:** `ContractController::show()` render `contracts.show` (`ContractController.php:130-145`) và InvoiceController render `invoices.show` (`InvoiceController.php:103-120`); cây view được liệt kê không có `resources/views/contracts/show.blade.php` hoặc `resources/views/invoices/show.blade.php`. Route resource vẫn bật show cho hai resource tại `routes/web.php:96,98`; khi gọi sẽ lỗi view không tồn tại.
- **Không thể xác nhận sản phẩm đáp ứng yêu cầu cài đặt/nộp:** README không có hướng dẫn dự án; thiếu SQL dump và migration/schema nghiệp vụ đầy đủ. Đây cũng là nguy cơ demo/nộp bài cao.

### Medium

- **Danh sách nghiệp vụ không phân trang:** room, tenant, contract, invoice dùng `get()`; search invoice/contract không giữ query string qua trang vì không có paginate. Khó đáp ứng yêu cầu tối thiểu mục 3.3 và có nguy cơ tải lớn.
- **Migration hợp đồng phụ thuộc cú pháp MySQL:** `2006_07_03_081404_add_property_id_to_contracts_table.php:26-30` dùng `UPDATE contracts c INNER JOIN rooms r ...`; cấu hình PHPUnit dùng SQLite in-memory (`phpunit.xml:23-24`). Sau khi bổ sung bảng, migration này vẫn cần xử lý tính tương thích nếu chạy test trên SQLite.
- **Seeder không tạo dữ liệu demo theo nghiệp vụ:** `DatabaseSeeder.php:18-24` chỉ tạo một user factory; không hỗ trợ demo trọn luồng và role theo yêu cầu hồ sơ.
- **Test nghiệp vụ và phân quyền chưa có bằng chứng:** bộ test đang là scaffold auth/profile và example; không có test cho CRUD cốt lõi, ranh giới property, trạng thái phòng/hợp đồng, hóa đơn hoặc route role. Chưa thể kết luận đây là lỗi runtime vì chưa chạy test.

### Low

- **Có `ManagerController` không được route tham chiếu trong `routes/web.php`:** khả năng code thừa/lỗi thời; cần xác nhận có chủ đích.
- **Ngôn ngữ tài liệu chưa phù hợp đối tượng nộp:** README hiển thị template tiếng Anh của Laravel; cần thay bằng hướng dẫn tiếng Việt hoặc ngôn ngữ nhóm chọn, cùng thông tin sản phẩm và tài khoản demo.

## 5. Điểm dự kiến và cách tính

File tiêu chí chỉ ghi trọng số và mô tả mức tối đa 8.5–10; tài liệu nói thang 5 mức theo “Rubric 2” trong đề cương học phần nhưng không đính kèm mức mô tả/cách quy đổi. Hai trong năm tiêu chí cần kiểm tra báo cáo, một cần bằng chứng làm việc nhóm và phần giao diện/chức năng chưa chạy được. Vì vậy **không có đủ căn cứ để đưa ra điểm dự kiến hợp lệ** mà không tự đặt thang điểm thay giảng viên. Trọng số được xác nhận là 10 + 10 + 30 + 40 + 10 = 100%. Cần xin Rubric 2 đầy đủ và xem báo cáo/demo/lịch sử làm việc nhóm trước khi tính điểm. Không tự quy trạng thái thành điểm số.

## 6. Tiêu chí chưa đủ bằng chứng

1. Hình thức và chất lượng báo cáo; số trang, font, caption, chính tả.
2. ERD, Use Case, mô tả phạm vi, tài liệu tham khảo, ảnh minh họa và phân công.
3. Đóng góp/tham gia của từng thành viên; code snapshot không chứng minh tương tác nhóm.
4. Giao diện thực tế, trải nghiệm theo vai trò, responsive và thông báo lỗi khi sử dụng.
5. Chức năng hoạt động end-to-end, bảo mật qua request thực, migration trên database sạch, dữ liệu seed.
6. Rubric 2 đầy đủ 5 mức để quy đổi điểm.
7. Slide/bảo vệ: tài liệu chỉ yêu cầu slide nếu lớp có buổi báo cáo; chưa biết điều kiện này.

## 7. Kế hoạch khắc phục ưu tiên

1. **P0 – Khôi phục khả năng dựng database sạch:** bổ sung/đối chiếu schema đầy đủ của rooms, tenants, contracts, invoices; đảm bảo thứ tự FK, kiểu dữ liệu, unique/index và rollback; sửa migration data backfill phụ thuộc MySQL hoặc xác định rõ chỉ hỗ trợ MySQL. Chạy migrate trên database thử nghiệm trống sau khi môi trường PHP/Composer sẵn sàng.
2. **P0 – Sửa routing hóa đơn và view show:** bỏ route root dư hoặc thiết kế controller/root route phù hợp; đảm bảo route có quyền/property scope; thêm view show hoặc bỏ action/route show nếu không thuộc phạm vi.
3. **P1 – Hoàn thiện danh sách chính:** thêm pagination cho phòng/người thuê/hợp đồng/hóa đơn; giữ query tìm kiếm khi chuyển trang; kiểm tra view paginator và UX.
4. **P1 – Hoàn thiện README và dữ liệu demo:** hướng dẫn yêu cầu PHP/Composer/Node, `.env`, key, database, migrate/seed, build/run; cung cấp tài khoản demo an toàn; seed property và dữ liệu liên quan; xác nhận DB trống dựng được.
5. **P1 – Bổ sung kiểm thử nghiệp vụ:** CRUD, validation, auth/logout, role admin/manager, truy cập chéo property, trạng thái phòng với hợp đồng, tính hóa đơn; chạy trên DB được hỗ trợ.
6. **P2 – Hoàn thiện hồ sơ nộp:** báo cáo đủ cấu trúc mục 6.1 và định dạng 6.2; ERD, Use Case, ảnh có chú thích; phân công thành viên; export SQL hoặc chứng minh migration+seed; slide nếu cần.
7. **P2 – Kiểm tra giao diện và sản phẩm trực tiếp:** walkthrough mỗi role, màn hình nhỏ, lỗi/empty state, kiểm tra tiếng Việt hiển thị đúng encoding; ghi nhận ảnh/video kết quả thực tế.
8. **P3 – Xin đề cương/Rubric 2 đầy đủ và đối chiếu nhóm:** dùng đúng mô tả 5 mức để ước lượng; bổ sung bằng chứng đóng góp Git/nhật ký/bảng phân công.

## Phụ lục: cách cài/chạy/kiểm thử theo tài liệu hiện có

- `composer.json` có script `setup` gồm `composer install`, tạo `.env` nếu thiếu, `php artisan key:generate`, `php artisan migrate --force`, `npm install --ignore-scripts`, `npm run build`.
- Script `dev` gọi `php artisan dev`; script `test` gọi `php artisan config:clear` rồi `php artisan test`.
- `.env.example` đặt `DB_CONNECTION=mysql`, database `quanlyphongtro`, session/cache/queue dùng database.
- README hiện không hướng dẫn các script này hay thiết lập MySQL.
- Không chạy các script trong review này: PHP và Composer không khả dụng; `setup` có tác động môi trường/database và `npm run build` ghi artifact. Không có test/build nào được tuyên bố là đã chạy.
