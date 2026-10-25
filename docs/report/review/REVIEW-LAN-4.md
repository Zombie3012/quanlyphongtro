# REVIEW LẦN 4 — Đồ án Quản lý phòng trọ

**Ngày review:** 25/10/2026  
**Nguồn tiêu chí:** `docs/report/tieuchi.docx`, mục 3.1–3.4, 5–8.  
**Đã đọc:** `docs/report/tieuchi.docx` và toàn bộ sáu tài liệu trong `docs/report/review/` (`BAO-CAO-REVIEW.md`, `REVIEW-LAN-2.md`, `REVIEW-LAN-3.md`, `REVIEW-FUNCTIONAL.md`, `REVIEW-INVOICE-RULE.md`, `REVIEW-INVOICE-IMPLEMENTATION.md`).  
**Phạm vi lượt 4:** Đối chiếu các hồ sơ trước với source, route, migration, README, seeder, test và artifact hiện có; không chạy lại full PHPUnit, browser walkthrough hoặc thử migration nâng cấp trên database đang dùng. Không tính điểm vì file rubric chỉ nêu yêu cầu mức tối đa và dẫn chiếu Rubric 2 đầy đủ nhưng không kèm rubric đó.

## 1. Kết luận

Source đã tiến bộ đáng kể so với review đầu: có route quản trị dưới `/admin`, route nghiệp vụ lồng theo khu trọ, migration dựng schema, seeder tạo dữ liệu nghiệp vụ, README hướng dẫn chạy, pagination/search cho các danh sách chính, và feature tests cho quản lý cùng hóa đơn. Lịch sử review ghi nhận suite mặc định từng đạt **35 tests / 177 assertions** sau khi loại test scaffold Breeze không áp dụng.

Tuy nhiên, có một regression kiểm thử phát sinh từ thay đổi gần nhất: seeder hiện tạo **3 khu trọ, 15 phòng, 12 người thuê, 10 hợp đồng và 18 hóa đơn**, còn `test_demo_seeder_creates_related_sample_data_and_is_repeatable()` vẫn assert mỗi bảng chỉ có một bản ghi. Test này sẽ thất bại với dữ liệu hiện tại. Vì vậy không nên dùng kết quả 35/35 trong phụ lục review lần 3 như kết quả của source hiện nay cho đến khi cập nhật assertion và chạy lại suite.

Ba nhóm việc còn quan trọng trước khi nộp là: **(1)** sửa và chạy lại test suite sau khi mở rộng seeder; **(2)** hoàn thành hồ sơ báo cáo/ERD/Use Case/phân công và xác nhận role `manager` với yêu cầu `User` trong rubric; **(3)** xác minh quy trình triển khai database hiện hữu nếu cần nâng cấp DB cũ. README và dữ liệu demo cơ bản đã được bổ sung, nhưng lượt review này chưa kiểm tra lại mọi yêu cầu trình bày hoặc mọi màn hình bằng browser.

## 2. Trạng thái theo rubric chính thức

| Tiêu chí | Trọng số | Trạng thái lần 4 | Bằng chứng / việc còn lại |
|---|---:|---|---|
| Hình thức báo cáo (CLO1) | 10% | **Chưa đủ bằng chứng** | Không thấy báo cáo đồ án `.docx`/`.pdf` trong workspace. `docs/report/tieuchi.docx` yêu cầu A4, Times New Roman 13, giãn dòng 1.5, tối thiểu 10 trang nội dung, đánh số trang, caption và tham chiếu hình. Các file review Markdown không thay thế báo cáo nộp. |
| Nội dung / chất lượng báo cáo (CLO5) | 10% | **Chưa đủ bằng chứng** | Chưa thấy báo cáo có bìa, mục lục, phạm vi, ERD, UML Use Case, mô tả chức năng, công nghệ, hướng dẫn sử dụng, kết quả theo role, phân công, hạn chế và tham khảo. |
| Giao diện phần mềm (CLO2) | 30% | **Đạt một phần** | Có Blade views theo module/role; review trước ghi nhận login/logout, trang chi tiết và một số màn hình mobile. Chưa có walkthrough hiện tại cho toàn bộ admin/manager, biểu mẫu lỗi, flash, trạng thái rỗng và mobile; cần ảnh minh họa có caption để đưa vào báo cáo. |
| Sản phẩm phần mềm (CLO4) | 40% | **Đạt phần lớn yêu cầu tối thiểu trong source; cần xác minh lại test sau thay đổi seeder** | Migrations + seeder hiện tạo được dữ liệu demo; auth/middleware, CRUD, search/pagination, validation/flash và logic hóa đơn có trong source. Review trước ghi nhận các test nghiệp vụ đạt. Test seeder hiện có assertion cũ nên suite mặc định chưa thể coi là xanh trên source hiện tại. Đường nâng cấp DB cũ vẫn chưa được chứng minh trên staging tương thích. |
| Tham gia thực hiện & tương tác nhóm (CLO3) | 10% | **Chưa đủ bằng chứng** | Không thấy danh sách thành viên 3–5 người, MSSV, bảng phân công/mức hoàn thành, PR/commit hoặc bằng chứng phối hợp. Cần bổ sung tài liệu nhóm; không suy ra đóng góp cá nhân từ source snapshot. |

**Không đưa ra điểm dự kiến.** Trọng số cộng lại là 100%, nhưng Rubric 2 đủ năm mức và bằng chứng báo cáo/nhóm chưa có.

## 3. Đối chiếu yêu cầu bắt buộc và tình trạng hiện tại

| Yêu cầu | Trạng thái | Nhận xét |
|---|---|---|
| Laravel, PHP, Eloquent, MySQL, migrations | **Có trong source/cấu hình** | `composer.json` khai báo Laravel 13/PHP 8.3; `.env.example` dùng MySQL; models có quan hệ Eloquent và migration dựng schema mới. Lần chạy ứng dụng trong workspace đã dùng SQLite cục bộ; trước khi demo/nộp theo mục 3.1 cần xác nhận lại trên MySQL sạch. |
| Database có thể dựng lại | **Đạt cho schema sạch theo bằng chứng trước; dữ liệu demo vừa được mở rộng** | Chuỗi migrations và `DatabaseSeeder` hiện tạo schema + bộ dữ liệu nghiệp vụ. Ở workspace hiện tại seeder chạy thành công và cho 3 properties, 15 rooms, 12 tenants, 10 contracts, 18 invoices. Cần chạy lại seed/migrate trên MySQL test sạch sau thay đổi seeder nếu MySQL là cấu hình nộp chính. |
| CRUD trên các bảng quan hệ | **Có trong source; có feature tests** | Có controller cho property, room, tenant, contract, invoice, user. `InvoiceBillingTest` và `ManagementFeaturesTest` kiểm tra nhiều luồng HTTP, validation, flash và phân quyền. Kết quả test đầy đủ cần chạy lại vì test dữ liệu seed hiện không khớp. |
| Login/logout, ít nhất hai role, middleware | **Có trong source; login/role đã được kiểm chứng trước** | Có `admin` và `manager`, `AdminMiddleware`, `PropertyAccess`, `/admin` riêng và route nghiệp vụ theo `properties/{property}`. Lịch sử review ghi nhận login/logout và ranh giới truy cập giữa các property đã hoạt động. |
| Role của đề tài khớp mục 3.2 | **Cần xác nhận/giải thích** | Rubric mô tả `User` như người dùng công khai có thể xem/tìm kiếm dữ liệu; ứng dụng dùng `manager` là nhân viên nội bộ quản lý property, và các trang nghiệp vụ cần đăng nhập. Đây có thể là cách cụ thể hóa vai trò Employee theo đề tài, nhưng báo cáo cần giải thích và nên xác nhận với giảng viên. Nếu giảng viên yêu cầu User công khai, hiện chưa thấy luồng xem/tìm kiếm công khai tương ứng. |
| Search + pagination + giữ từ khóa | **Đạt trong source cho các danh sách chính** | Property, room, tenant, contract và invoice dùng tìm kiếm, `paginate()` và `withQueryString()`. User list có pagination nhưng không thấy tìm kiếm; rubric chỉ yêu cầu ít nhất một danh sách chính. Test quản lý có kiểm tra query/pagination cho các module nghiệp vụ. |
| Validation và thông báo sau CRUD | **Có trong source và test** | Controller dùng validation, form có hiển thị lỗi; thao tác trả flash message. `ManagementFeaturesTest` có các test validation/flash cho room, property, tenant, contract; invoice suite kiểm tra hóa đơn. Cần chạy suite lại sau chỉnh sửa hiện tại. |
| Hóa đơn đúng công thức nghiệp vụ | **Đã triển khai theo chính sách đã chốt trong hồ sơ trước** | Công thức là `rent_amount + electricity + water + service_fee + other_fee`; server tự tính, snapshot tiền thuê, chặn sửa/xóa hóa đơn đã thanh toán hoặc hóa đơn cũ chưa đối soát. `REVIEW-INVOICE-IMPLEMENTATION.md` ghi nhận 17 test hóa đơn đạt trước đó. Migration snapshot trên bản sao DDL thật từng lỗi MariaDB 1041; không tự áp dụng lên DB hiện hữu. |
| Tách khu admin tại `/admin` | **Đạt trong source** | `routes/web.php` có dashboard admin, property CRUD dưới `/admin/properties` và user management dưới `/admin/users`; admin được middleware bảo vệ. |
| Mục 5 — tính năng mở rộng | **Chưa đủ bằng chứng để tính điểm cộng** | Có role middleware tùy chỉnh và một số kiểm tra mobile trong test/review. Chưa có bằng chứng đầy đủ cho upload, AJAX hoặc API ngoài; các mục này là tùy chọn. Responsive vẫn cần walkthrough các màn hình còn lại. |
| README cho máy khác | **Đạt một phần đến phần lớn** | README hiện có bước cài dependencies, cấu hình `.env`, migrate/seed/build/run, tài khoản demo và mật khẩu chung `12345678`; ghi rõ chỉ dùng local/testing. Cần xác minh lại toàn bộ hướng dẫn từ database MySQL mới và có thể ghi thêm URL/khắc phục lỗi môi trường cụ thể nếu nhóm muốn hướng dẫn tự chứa hơn. |
| File nộp theo mục 7 | **Chưa đủ** | Có source, migrations, seeder và README. Không thấy SQL export, báo cáo DOCX/PDF, ERD/Use Case hoặc slide trong workspace; slide chỉ cần nếu lớp có buổi bảo vệ. Migrations + seeder là một lựa chọn hợp lệ theo rubric nếu chứng minh chạy được từ database trống. |

## 4. Phát hiện cần xử lý

### P0 — Cập nhật test seeder và chạy lại suite

`tests/Feature/ManagementFeaturesTest.php::test_demo_seeder_creates_related_sample_data_and_is_repeatable()` vẫn assert `properties=1`, `rooms=1`, `tenants=1`, `contracts=1`, `invoices=1`. `DemoDataSeeder` hiện tạo 3/15/12/10/18. Khi test gọi seeder hai lần trên database test trống, các assertion cũ không còn đúng. Cập nhật kỳ vọng và thêm assertion mật khẩu demo nếu cần; sau đó chạy suite mặc định trên SQLite in-memory. Không dùng database demo cho kiểm thử.

Phụ lục cuối `REVIEW-LAN-3.md` ghi 35 test pass trước thay đổi seeder; phần này đã lỗi thời đối với source hiện tại. Kết quả 42 test có lỗi ở phần đầu review lần 3 là kết quả trước khi suite được căn chỉnh, không phải kết quả mới nhất.

### P1 — Chốt cách diễn giải role User

Rubric 3.2 mô tả Admin và User; ví dụ User có thể xem/tìm kiếm dữ liệu công khai. Sản phẩm đang dùng manager nội bộ, quản lý dữ liệu theo property. Viết rõ trong báo cáo manager tương ứng vai trò nhân viên vận hành của đề tài và xin xác nhận giảng viên. Nếu không được chấp nhận, cần bổ sung actor User phù hợp thay vì chỉ đổi tên role trong tài liệu.

### P1 — Xác minh nâng cấp database đã có dữ liệu

Migration snapshot `rent_amount` từng thất bại với MariaDB 10.4.13 lỗi `1041 Out of memory` trên bản sao DDL thật. DB hiện hữu không có migration history theo các review trước. Chỉ triển khai trên staging/bản sao có thể khôi phục sau khi baseline và migration được thử thành công, đối chiếu dữ liệu trước/sau và chuẩn bị backup/rollback. Nếu chỉ nộp fresh install, cung cấp hướng dẫn tạo database mới và không chạy `migrate` mù lên DB cũ.

### P1 — Hoàn thiện hồ sơ báo cáo và nhóm

Tạo báo cáo theo mục 6.1/6.2, kèm ERD, Use Case, ảnh màn hình theo đúng vai trò, tài liệu tham khảo, hạn chế/hướng phát triển, bảng thành viên/MSSV và phân công/mức hoàn thành. Lưu DOCX/PDF và sơ đồ trong thư mục nộp để có thể kiểm tra. Xin Rubric 2 đầy đủ nếu muốn tự đánh giá theo năm mức chính thức.

### P2 — Walkthrough UI và kiểm tra môi trường nộp

Kiểm tra admin và từng manager trên MySQL/demo data, desktop/mobile, empty state, validation/flash, chi tiết hợp đồng/hóa đơn và bảng lớn. Lưu ảnh có caption/tham chiếu. Trước buổi demo, xác nhận `.env` trỏ MySQL theo yêu cầu rubric; SQLite đang dùng trong workspace hiện tại chỉ là môi trường tiện chạy local.

### P3 — Tùy chọn cộng điểm

Upload, AJAX, API ngoài là tùy chọn. Chỉ chọn tính năng nhóm có thể hoàn thiện và trình bày được; không cần thêm chúng để đạt các yêu cầu bắt buộc. Responsive tốt có thể là hạng mục cộng điểm nhưng cần bằng chứng trên các màn hình chính.

## 5. Việc đã có thể coi là hoàn tất theo bằng chứng hiện tại

- README mô tả cách cài/chạy, lệnh seed và bốn tài khoản demo; mật khẩu demo là `12345678`.
- Seeder mới đã chạy được trên SQLite hiện tại, tạo 3 khu trọ, 15 phòng, 12 người thuê, 10 hợp đồng, 18 hóa đơn; chạy lại không tăng số lượng bản ghi.
- PHP lint cho seeder mới không báo lỗi cú pháp.
- Route admin tập trung tại `/admin`; route hợp đồng/hóa đơn nằm trong phạm vi property.
- Search và pagination đã có cho các danh sách chính, giữ truy vấn qua trang.
- Hóa đơn có công thức/snapshot và policy được chốt trong review triển khai hóa đơn; test HTTP riêng đã từng đạt 17/17.

## 6. Checklist ngắn để hoàn tất đồ án

1. Sửa các assertion dữ liệu trong test seeder; chạy lại suite mặc định và lưu kết quả mới.
2. Dựng database MySQL sạch, chạy toàn bộ migration + `db:seed`, thử login bằng bốn tài khoản demo; giữ DB đó làm bằng chứng cài đặt tái lập.
3. Hỏi giảng viên về cách chấp nhận role `manager` so với `User` trong rubric; ghi câu trả lời và phạm vi vào báo cáo.
4. Hoàn thành báo cáo DOCX/PDF, ERD, Use Case, ảnh có caption/tham chiếu và bảng phân công nhóm.
5. Walkthrough UI theo role trên desktop/mobile; lưu ảnh và ghi lại các luồng đạt/chưa đạt.
6. Nếu triển khai lên database hiện hữu, kiểm tra baseline + migration trên staging có backup/restore; nếu chỉ nộp database mới, ghi rõ quy trình fresh install và không áp dụng migration lên DB cũ.
7. Xác nhận deadline cụ thể, yêu cầu slide/bảo vệ và lấy Rubric 2 đủ năm mức từ giảng viên/đề cương.

## 7. Tài liệu và source đã đối chiếu

- `docs/report/tieuchi.docx`; sáu file review được liệt kê ở đầu báo cáo.
- `README.md`, `composer.json`, `.env.example`, `phpunit.xml`, `routes/web.php`.
- Controllers, models, middleware, migrations, seeders và factories.
- `tests/Feature/ManagementFeaturesTest.php`, `tests/Feature/InvoiceBillingTest.php` và test scaffold.
- Artifact được tìm thấy: rubric DOCX và ảnh login; chưa thấy báo cáo đồ án, ERD, Use Case, SQL export hay slide.

