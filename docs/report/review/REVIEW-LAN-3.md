# REVIEW LẦN 3 — Đối chiếu tiêu chí đồ án Quản lý phòng trọ

**Ngày review:** 22/10/2026  
**Nguồn tiêu chí:** `docs/report/tieuchi.docx`, các mục 3.1–3.4, 5–8.  
**Phạm vi:** Đọc lại rubric gốc, các review trước, README và source hiện tại; chạy lại InvoiceBillingTest, full PHPUnit suite, route:list và frontend production build.  
**Yêu cầu của chủ đồ án:** Hoãn đánh giá nội dung/hình thức báo cáo. Hai tiêu chí liên quan báo cáo được ghi nhận là **chưa kiểm tra trong lượt này**, không suy diễn là đã hoàn thành hoặc chưa tồn tại.

## 1. Kết luận chung

Đồ án đã có ứng dụng Laravel thực sự với các module quản lý khu trọ, phòng, người thuê, hợp đồng, hóa đơn và tài khoản. Bằng chứng hiện tại gồm source Eloquent/Blade, middleware, migration, route, feature tests cho hóa đơn, kiểm thử runtime đăng nhập/phân quyền trong `REVIEW-FUNCTIONAL.md`, cùng frontend build thành công.

Không có cơ sở chấm điểm số chính xác: `docs/report/tieuchi.docx` xác nhận trọng số năm tiêu chí tổng 100%, nhưng phần mô tả chi tiết năm mức được dẫn chiếu sang “Rubric 2” của đề cương và không đính kèm. Báo cáo này vì vậy nêu điểm tối đa chính thức, trạng thái và thiếu sót; không quy đổi trạng thái thành điểm tự đặt.

Những phần còn cần ưu tiên trước khi nộp sản phẩm: hoàn tất hướng dẫn README theo dự án; cung cấp database export hoặc quy trình migrations + seeder có dữ liệu demo tái lập; xử lý/ghi rõ rủi ro áp dụng migration snapshot lên database cũ; và quyết định có sửa bộ test scaffold để toàn suite xanh hay loại bỏ/căn chỉnh các test không thuộc chức năng dự án. Hồ sơ báo cáo và bằng chứng nhóm đang được hoãn theo yêu cầu của người dùng.

## 2. Rubric chính thức — trạng thái theo từng tiêu chí

| Tiêu chí chính thức | Điểm tối đa | Trạng thái hiện tại | Bằng chứng cụ thể | Còn thiếu / bổ sung tối thiểu |
|---|---:|---|---|---|
| Hình thức báo cáo (CLO1) | 10 | **Chưa kiểm tra trong lượt này** | Người dùng yêu cầu để báo cáo làm cuối. Không dùng các file review Markdown làm bằng chứng thay cho báo cáo đồ án. Yêu cầu định dạng nằm ở `docs/report/tieuchi.docx`, mục 6.2. | Khi làm báo cáo cuối, đối chiếu font/cỡ/dãn dòng/A4, số trang, caption/tham chiếu, chính tả và tính nhất quán. |
| Nội dung / chất lượng báo cáo (CLO5) | 10 | **Chưa kiểm tra trong lượt này** | Người dùng yêu cầu bỏ qua báo cáo. Mục 6.1 liệt kê các phần bắt buộc; chưa kiểm tra bản báo cáo nộp. | Đối chiếu đủ cấu trúc ở mục 6.1, gồm ERD, Use Case, mô tả chức năng, hướng dẫn, kết quả theo vai trò, phân công, hạn chế và tham khảo. |
| Giao diện phần mềm (CLO2) | 30 | **Đạt một phần** | Có Blade cho login, dashboard, property, room, tenant, contract, invoice, user. `REVIEW-FUNCTIONAL.md` ghi nhận login/logout, dashboard theo role, trang danh sách và chi tiết hợp đồng/hóa đơn đã render với tài khoản/record mẫu. `npm run build` lần này thành công (Vite 8.3.1), output được đặt ngoài repository. | Chưa có walkthrough trực quan hiện tại với ảnh/chụp màn hình cho mọi vai trò, trạng thái lỗi/rỗng và viewport mobile. Chưa có đánh giá đủ căn cứ về thẩm mỹ/UX. Mục 3.2 mô tả admin vào khu vực riêng `/admin`; hiện user admin tại `/admin/users`, CRUD khu trọ ở `/properties`, chưa có trang quản trị tổng `/admin`. |
| Sản phẩm phần mềm (chức năng) (CLO4) | 40 | **Đạt một phần** | Laravel 13/PHP 8.3 trong `composer.json`; Eloquent relationships ở models; migrations; middleware role/property; source CRUD và validation; property search+pagination; invoice feature suite đạt 17/17 test, 85 assertions; kiểm thử trước đó xác nhận login/logout/role/property scope bằng bốn tài khoản. | Full test suite không xanh (21 pass, 19 fail, 2 errors). Phần lớn lỗi do test scaffold Breeze/profile không khớp routes/login hiện tại; cần quyết định căn chỉnh suite. Seeder chưa tạo dữ liệu nghiệp vụ để demo. Không thấy README dự án hoặc SQL export. Migration snapshot đã thất bại trên bản sao schema thật trong kiểm tra trước (MariaDB 1041); chưa xác nhận đường nâng cấp database hiện hữu an toàn. |
| Tham gia thực hiện & tương tác nhóm (CLO3) | 10 | **Chưa đủ bằng chứng / hoãn cùng hồ sơ báo cáo** | Workspace không cung cấp đủ nhật ký Git/PR, phân công, đánh giá cá nhân hoặc bằng chứng trao đổi nhóm. `docs/report/tieuchi.docx` mục 3.1 và 8 yêu cầu 3–5 thành viên và đóng góp tương đối đồng đều. | Bổ sung danh sách nhóm, phân công/mức hoàn thành và bằng chứng đóng góp/tương tác phù hợp. Không thể suy ra đóng góp cá nhân từ source snapshot. |

**Trọng số chính thức:** 10 + 10 + 30 + 40 + 10 = **100%**. Không tính tổng điểm dự kiến vì chưa có Rubric 2 đầy đủ và hai tiêu chí được hoãn.

## 3. Đối chiếu yêu cầu kỹ thuật và hồ sơ sản phẩm

Trạng thái: **Đạt** = có bằng chứng đủ theo yêu cầu tối thiểu; **Đạt một phần** = có implementation nhưng còn thiếu điều kiện/evidence; **Chưa đạt** = artifact hiện có chưa đáp ứng; **Chưa đủ bằng chứng** = chưa thể xác nhận.

| Mục tiêu trong tài liệu | Trạng thái | Bằng chứng / phần còn thiếu |
|---|---|---|
| 3.1 — Laravel, PHP, MySQL/Eloquent | **Đạt trong source/cấu hình; hoạt động một phần đã kiểm tra** | `composer.json` khai báo PHP `^8.3`, Laravel `^13.17`; PHP 8.3.30 khả dụng. Models có relationships; `.env.example` dùng MySQL; DB mẫu MySQL đã được đọc trong lần review trước. Không đánh giá lại credential/config bí mật. |
| 3.1 — cấu trúc DB bằng migrations | **Đạt trên DB sạch; chưa đủ cho triển khai DB hiện hữu** | `database/migrations/2006_07_03_080820_create_rental_core_tables.php` tạo rooms, tenants, contracts, invoices; các migration khác bổ sung property scope và user fields; `2026_10_22_000000_add_rent_amount_snapshot_to_invoices.php` thêm snapshot. Review triển khai hóa đơn ghi nhận migrations chạy trên DB test sạch. Trên bản sao DDL gần DB thật, migration snapshot gặp lỗi MariaDB `1041 Out of memory`; không chạy migration trên DB mẫu. |
| 3.1 — Seeder/Factory tạo dữ liệu mẫu | **Đạt một phần** | Có factory cho User, Property, Room, Tenant, Contract, Invoice (`database/factories/`). `DatabaseSeeder` hiện chỉ tạo một tài khoản `Test User`; không seed khu trọ/phòng/người thuê/hợp đồng/hóa đơn để walkthrough. |
| 3.1 — Blade và giao diện tách khu vực | **Đạt một phần** | Blade layout chung và views theo module/role; admin user route nằm `/admin/users`, property management không nằm dưới `/admin`. Chưa xác nhận khu vực UI phân tách đầy đủ theo cách diễn giải rubric. |
| 3.2 — tối thiểu hai role, Auth + Middleware | **Đạt cho luồng đã kiểm tra** | `AuthController`, `AdminMiddleware`, `PropertyAccess`, route group auth/admin/property. Review runtime lần trước đã ghi login/logout thành công; admin truy cập mọi property; manager chỉ property được gán; truy cập trái quyền bị 403/404. Tài liệu gọi role thứ hai là User công khai theo ví dụ chung; đồ án dùng `manager`, nên cần diễn giải trong báo cáo rằng manager là actor của nghiệp vụ và xác nhận yêu cầu này phù hợp đề tài. |
| 3.2 — admin vào khu vực riêng `/admin` | **Đạt một phần** | Có `/admin/users`; không có route trang `/admin` tổng. Property CRUD nằm ở `/properties`. Cần xác nhận rubric yêu cầu prefix chính xác hay chỉ yêu cầu khu vực quản trị được bảo vệ. |
| 3.3 — CRUD ít nhất hai bảng có quan hệ | **Đạt trong source; invoice đã kiểm tra end-to-end trên test DB** | Controllers CRUD cho property, room, tenant, contract, invoice. Relationships ở `Property`, `Room`, `Tenant`, `Contract`, `Invoice`. `InvoiceBillingTest` kiểm thử HTTP create/update/show và các nhánh nghiệp vụ trên database test. CRUD các module ngoài invoice chưa có feature coverage tương đương trong suite. |
| 3.3 — login/logout và phân quyền middleware | **Đạt cho các trường hợp runtime đã thử** | Review chức năng trước ghi nhận bốn tài khoản đăng nhập/logout, role redirect và property scope qua GET; test suite hiện có một số auth scaffold tests không khớp app. Chưa coi kết quả GET là chứng minh CRUD ghi chéo đã bị chặn; invoice feature test bao phủ một số scope. |
| 3.3 — search + pagination ít nhất một danh sách chính, giữ từ khóa | **Đạt yêu cầu tối thiểu trong source** | `PropertyController::index` áp dụng keyword/status, `paginate(10)->withQueryString()`; property index render paginator. Các danh sách room/tenant/contract/invoice có search nhưng kết thúc bằng `get()`; user paginate nhưng không có search. Pagination ngoài property là cải thiện hữu ích, nhưng không bắt buộc để thỏa đúng câu chữ “ít nhất 1 danh sách”. |
| 3.3 — validation và thông báo lỗi | **Đạt một phần** | Controllers dùng `$request->validate()`; nhiều Blade form render `@error`. Bộ invoice có test giá trị âm, thiếu/sai định dạng và kiểm tra feedback. Các module khác chưa có test đủ về response validation/old input. |
| 3.3 — flash sau CRUD | **Đạt trong source; được kiểm chứng cho invoice** | Controllers trả `with('success', ...)`; view danh sách hiển thị flash. Invoice suite assert session success. Chưa xác nhận flash sau mọi loại CRUD/property/user bằng HTTP test. |
| 3.4 — mô tả đề tài, ERD, Use Case, chức năng có ảnh, phân công | **Chưa kiểm tra theo yêu cầu hoãn báo cáo** | Không đánh giá các tài liệu báo cáo ở lượt này. |
| 5 — tính năng mở rộng | **Chưa có bằng chứng cộng điểm** | Có middleware role tùy chỉnh; chưa xác nhận upload/Storage, AJAX hoặc API bên ngoài. Meta viewport có trong layout/login nhưng chưa chứng minh responsive tốt trên thiết bị thật. Không tính điểm cộng khi chưa có bằng chứng. |
| 7 — mã nguồn, DB export hoặc migrations+seeder | **Đạt một phần** | Source hiện có migration dựng schema sạch đã được chạy trên test DB theo `REVIEW-INVOICE-IMPLEMENTATION.md`; chưa thấy file `.sql` export. Seeder chỉ tạo user; thiếu seed dataset nghiệp vụ. DB mẫu local đang chạy không được đóng gói cùng source. |
| 7 — README trên máy khác | **Chưa đạt** | `README.md` vẫn là template Laravel, không ghi cài dependencies của project, cấu hình DB, migrate/seed/import, tài khoản demo, build/run hoặc URL. |
| 7 — slide | **Chưa đủ bằng chứng** | Chỉ cần nếu lớp có buổi bảo vệ; điều kiện lớp chưa được cung cấp, và phần hồ sơ báo cáo được hoãn. |

## 4. Kiểm thử thực tế trong lượt này

| Kiểm tra | Kết quả thực tế | Phạm vi và giới hạn |
|---|---|---|
| `php artisan test --compact --filter=InvoiceBillingTest` | **17 passed, 85 assertions** | Chạy bằng PHP 8.3.30, `DB_CONNECTION=mysql`, `DB_DATABASE=quanlyphongtro_invoice_test_20261022`, session array. Đây là DB test riêng đã dùng trong review hóa đơn; không dùng DB demo. Bao phủ công thức, validation, snapshot, duplicate month, scope, legacy/paid policy, flash và HTTP invoice flows. |
| `php artisan test --compact` | **42 tests: 21 passed, 19 failed, 2 errors, 114 assertions** | Chạy cùng DB test riêng. Failures nổi bật: auth scaffold mong email-login nhưng sản phẩm dùng username; logout test mong redirect `/` trong khi app chuyển `/login`; tests email verification/password confirmation/reset/registration/profile gọi routes chưa khai báo. Điều này chứng minh suite hiện không xanh; không chứng minh tất cả chức năng ứng dụng hỏng vì một phần là scaffold không thuộc phạm vi app. |
| `php artisan route:list --except-vendor` | **Thành công; hiển thị 46 routes** | Invoice/contract routes nằm dưới `properties/{property}`; admin-only `/admin/users`; không có `/admin` root. Chỉ xác minh route registration, không thay thế kiểm thử nghiệp vụ. |
| `npm run build -- --outDir C:\Users\Administrator\AppData\Local\Temp\quanlyphongtro-review3-build-20261022` | **Thành công, Vite 8.3.1** | Output manifest/CSS/JS ghi vào thư mục Temp, không ghi `public/build`. Đây là xác nhận build frontend, không phải browser test. |
| Chạy CRUD create/update/delete cho room/tenant/contract/property/user trên MySQL test | **Chưa chạy trong lượt này** | Không cần ghi thêm dữ liệu để xác nhận rubric tối thiểu vì invoice HTTP suite đã chạy; kiểm thử các luồng còn lại cần test cases tương ứng. Có thể bổ sung feature tests trên DB test riêng. Không thao tác DB demo. |
| Browser walkthrough UI/responsive | **Chưa chạy trong lượt này** | Không dùng HTTP 200/build làm bằng chứng thay cho đánh giá hình ảnh, UX hoặc responsive. Review trước đã xác nhận một số màn hình render với tài khoản mẫu nhưng không có bộ screenshot/viewport bao phủ toàn app. |
| Migration update trên DB kiểu hiện hữu | **Không chạy lại trong lượt này; lần thử trước thất bại trên bản sao** | `REVIEW-INVOICE-IMPLEMENTATION.md` ghi migration snapshot thử trên bản sao DDL không có dữ liệu thật, gặp MariaDB 10.4.13 lỗi 1041. Không áp dụng migration lên DB demo; cần staging có thể restore và tài nguyên phù hợp. |

## 5. Lỗi/rủi ro theo mức độ

### High

1. **Chưa có hướng dẫn cài đặt máy khác:** README template không đáp ứng Mục 7. Thiếu hướng dẫn DB, migrate/import, seed, build/run và account demo.
2. **Artifact dựng DB demo chưa hoàn chỉnh:** không thấy SQL export; seeder chỉ tạo một user. Migrations đã dựng được DB sạch theo kiểm tra đã ghi, nhưng không có dữ liệu nghiệp vụ demo tái lập; migrations cho DB mới không tự giải quyết được database hiện hữu thiếu migration history.
3. **Rủi ro migration trên schema đang dùng:** migration thêm `rent_amount` thất bại với MariaDB 1041 trên bản sao cấu trúc DB thật theo kiểm tra trước. Chưa có quy trình baseline/deploy/restore đã được xác minh cho DB mẫu hiện hữu.

### Medium

1. **Full PHPUnit suite còn 21 pass, 19 fail, 2 errors.** Các lỗi chủ yếu bắt nguồn từ test Breeze/profile/email routes không thuộc hoặc không trùng phương thức auth hiện tại; để nộp có độ tin cậy, cần đồng bộ test suite với tính năng thật hoặc loại test mẫu không áp dụng và đảm bảo test nghiệp vụ cốt lõi được chạy.
2. **Không có feature tests bao phủ CRUD/phân quyền cho mọi module.** Invoice suite là điểm mạnh mới; các module property/room/tenant/contract/user phần lớn mới có source và runtime GET/role evidence.
3. **Chưa có `/admin` landing route, và CRUD property ở `/properties`.** Cần đối chiếu cách hiểu yêu cầu “khu vực quản trị riêng (/admin)” với giảng viên; phần nhỏ nhất có thể là điều hướng/landing nhất quán nếu cần.
4. **Chỉ property có pagination.** Đây vẫn đạt mức tối thiểu ở Mục 3.3. Danh sách nghiệp vụ khác chưa paginate; đây là giới hạn mở rộng/UX khi dữ liệu tăng, không tự kết luận là trượt yêu cầu “ít nhất 1 danh sách”.

### Low / chưa có bằng chứng

1. **Chưa xác nhận responsive thực tế hoặc chất lượng thẩm mỹ trên mobile.** Build và viewport metadata không chứng minh trải nghiệm.
2. **Không thấy bằng chứng tính năng cộng điểm upload/AJAX/API ngoài.** Mục 5 là tùy chọn; không nên triển khai chỉ để đủ danh sách nếu không cần.
3. **Không đủ hồ sơ để đánh giá đóng góp nhóm và yêu cầu báo cáo.** Hai nội dung này đang hoãn/chờ hồ sơ.

## 6. Danh sách chưa làm/chưa đủ bằng chứng

1. Báo cáo DOCX/PDF: cấu trúc, 10 trang, font, line spacing, ảnh, caption, tài liệu tham khảo — **chưa kiểm tra theo yêu cầu**.
2. ERD, UML Use Case, mô tả đề tài/phạm vi, screenshot minh họa và bảng phân công — **chưa kiểm tra theo yêu cầu hoãn báo cáo**.
3. Đóng góp/tương tác từng thành viên, danh sách nhóm 3–5 người — **chưa đủ bằng chứng**.
4. README cài đặt/chạy trên máy khác — **chưa đạt**.
5. SQL export hoặc bộ migrations + seeder tạo schema và dữ liệu nghiệp vụ demo — **chưa đủ**.
6. Browser walkthrough đủ vai trò, validation/flash, empty states, mobile — **chưa đủ bằng chứng**.
7. CRUD HTTP tests cho property, room, tenant, contract, user; username giữ nguyên/đổi/trùng — **chưa đủ test tự động trong suite hiện tại**.
8. Kiểm tra update migration trên staging đủ tài nguyên/khả năng restore — **chưa đạt/đang chặn triển khai schema update cho DB hiện hữu**.
9. Rubric 2 đủ năm mức — không có trong file được cung cấp; do đó **không thể tính điểm dự kiến**.
10. Slide nếu lớp tổ chức bảo vệ — **chưa đủ thông tin**.

## 7. Kế hoạch ưu tiên trước khi nộp

1. **P0 — Chốt artifact cài đặt database:** chọn SQL export đã ẩn danh hoặc migrations+seeder hoàn chỉnh; xác minh từ database test sạch rằng schema và dữ liệu demo có thể dựng lại. Lập quy trình riêng cho DB đang có dữ liệu, không chạy migration mù trên DB hiện hữu.
2. **P0 — Viết README dự án:** prerequisites, `.env`, tạo key, import/migrate/seed, build frontend, lệnh chạy, URL và cách có account demo an toàn.
3. **P1 — Khóa rủi ro migration snapshot:** tìm nguyên nhân lỗi 1041 trên staging có thể restore; xác nhận upgrade path và kiểm tra giữ nguyên total các invoice cũ. Đừng dùng DB mẫu làm môi trường thử migration.
4. **P1 — Căn chỉnh test suite:** giữ 17 invoice tests đang xanh; phân loại các test scaffold không thuộc sản phẩm; bổ sung feature tests tối thiểu cho property/room/tenant/contract/user và kiểm thử username. Mục tiêu là test suite thể hiện đúng hợp đồng chức năng sản phẩm.
5. **P1 — Xác nhận cách tổ chức `/admin`:** hỏi giảng viên nếu prefix là yêu cầu cứng; nếu có, thêm landing/điều hướng tối thiểu, giữ nguyên CRUD/middleware đã có.
6. **P2 — Walkthrough UI:** kiểm tra từng role trên desktop và viewport điện thoại; chụp màn hình các chức năng, validation, flash, dữ liệu rỗng và chi tiết để dùng trong hồ sơ cuối.
7. **P2 — Hoàn thiện báo cáo và nhóm ở lượt cuối:** đối chiếu Mục 6.1/6.2, Rubric 2 đầy đủ, ERD/Use Case, tài liệu tham khảo, phân công và bằng chứng đóng góp.

## 8. Danh sách cuối theo yêu cầu

### A. Đã đạt, không cần sửa chỉ để đáp ứng rubric tối thiểu

- Ứng dụng dùng Laravel, PHP, Blade/Tailwind và Eloquent models/relationships.
- Có ít nhất hai role với middleware auth/admin/property access; runtime review trước đã xác nhận login/logout, route admin và property scope cho các tài khoản mẫu.
- Có CRUD cho nhiều bảng quan hệ trong source; riêng invoice có 17 feature tests qua HTTP trên DB test riêng.
- Mục tìm kiếm + phân trang tối thiểu đã có trên property list và giữ query bằng `withQueryString()`.
- Frontend build thành công trong lượt này.
- Route show contract/invoice hiện có view và scoped dưới property route; route:list xác nhận đăng ký.

### B. Có chức năng nhưng cần bổ sung hoặc hoàn thiện

- Migration/schema tái tạo DB sạch có bằng chứng đã chạy; cần seeder dữ liệu nghiệp vụ và xác minh upgrade path database đang tồn tại.
- Validation/flash có trong các controllers/views; mức kiểm thử tự động tốt hiện tập trung ở invoices.
- Vai trò manager hợp lý với nghiệp vụ nhưng cần mô tả rõ quan hệ với yêu cầu User trong mục 3.2; `/admin` chưa là khu vực quản trị thống nhất.
- Pagination ở room/tenant/contract/invoice chưa có; không phải điều kiện tối thiểu nếu property list được chấp nhận là danh sách chính.
- Full PHPUnit suite còn lỗi scaffold; cần làm rõ trước khi dùng làm bằng chứng chất lượng.
- Responsive, UX và trạng thái giao diện chưa được walkthrough đầy đủ.

### C. Còn thiếu, cần thực hiện trước khi nộp

- README hướng dẫn cài/chạy dự án trên máy khác.
- DB artifact nộp bài có thể tái lập: SQL export ẩn danh hoặc migrations + seeder tạo schema và bộ dữ liệu demo nghiệp vụ.
- Xác minh đường nâng cấp migration `rent_amount` trên staging; kiểm tra trước/sau và có phương án restore.
- Báo cáo DOCX/PDF, ERD, Use Case, screenshot caption/tham chiếu, phân công — đang hoãn theo yêu cầu, nên chưa thể coi là đạt.
- Bằng chứng đóng góp/tương tác nhóm và Rubric 2 đầy đủ — chưa có trong workspace/bản tiêu chí được cung cấp.

## Phụ lục — file và vùng source đã đối chiếu

- Rubric gốc: `docs/report/tieuchi.docx`, mục 3.1–3.4, 5, 6, 7, 8.
- Tài liệu cũ: `docs/report/review/BAO-CAO-REVIEW.md`, `docs/report/review/REVIEW-LAN-2.md`, `docs/report/review/REVIEW-FUNCTIONAL.md`, `docs/report/review/REVIEW-INVOICE-RULE.md`, `docs/report/review/REVIEW-INVOICE-IMPLEMENTATION.md`.
- Cấu hình: `README.md`, `composer.json`, `.env.example`, `phpunit.xml`, `package.json`.
- Route/auth/permission: `routes/web.php`, `app/Http/Controllers/AuthController.php`, `app/Http/Middleware/AdminMiddleware.php`, `app/Http/Middleware/PropertyAccess.php`.
- Nghiệp vụ: controllers/models trong `app/Http/Controllers` và `app/Models`.
- Giao diện: `resources/views` cho login, dashboard, properties, rooms, tenants, contracts, invoices, users và layout.
- Database/tests: `database/migrations`, `database/factories`, `database/seeders/DatabaseSeeder.php`, `tests/Feature/InvoiceBillingTest.php`, các test Auth/Profile scaffold.

## Cập nhật sau khi hoàn thiện các hạng mục được chọn

**Lưu ý:** Bảng A/B/C và kết luận phía trên là ảnh chụp trước đợt triển khai. Với sáu nội dung được người dùng yêu cầu, trạng thái hiện hành là phần cập nhật này; nội dung chưa được triển khai được nêu rõ bên dưới.

העדכון להלן מתעד את העבודה והבדיקות שבוצעו אחרי יצירת גרסה זו. סעיפי הדוח על CLO1/CLO5 נשארים ללא שינוי: תוכן/צורת דוח ההגשה הוחרגו theo yêu cầu.

### Đã thực hiện trong source

- Đã thêm `DemoDataSeeder` chạy idempotent trong môi trường `local`/`testing`, tạo tài khoản demo, property, room, tenant, contract và invoice nhất quán. Mật khẩu demo lấy từ `DEMO_ACCOUNT_PASSWORD`, mặc định buộc đổi trước khi dùng ngoài máy local. `DatabaseSeeder` gọi seeder này.
- Đã thêm lệnh `app:baseline-existing-database` với mặc định chỉ dry-run; lệnh kiểm tra schema rồi báo migration history dự kiến. Thử nghiệm trên DB tách biệt: baseline schema cũ, ghi history khi có `--apply`, rồi chạy migration snapshot thành công. Migration history không được ghi lên DB demo.
- Đã thay migration backfill hợp đồng sang Query Builder để tương thích SQLite/MySQL; trên fresh SQLite suite toàn bộ test mặc định chạy được.
- Đã thêm feature tests cho login/logout, admin/manager route scope, username giữ nguyên/đổi mới/trùng, validation/flash, tìm kiếm/phân trang, seeder idempotence và các luồng cốt lõi. Breeze/profile scaffold tests được giữ nguyên nhưng loại khỏi suite mặc định vì ứng dụng không cài các route đó.
- Đã thêm pagination và `withQueryString()` cho danh sách phòng, người thuê, hợp đồng và hóa đơn; trang phòng có form tìm kiếm.
- Đã gom khu quản trị về `/admin`, thêm dashboard quản trị và chuyển property management sang `/admin/properties`; các route manager vẫn lồng property và qua middleware `property.access`.
- README được thay bằng hướng dẫn cài/chạy, phân biệt vai trò admin và manager, hướng dẫn seed/baseline, và cảnh báo dùng DB riêng khi thử baseline.
- Các view component Blade cần bởi view auth/profile scaffold được bổ sung để quá trình compile/cache view ổn định. View cache thành công.

### Kết quả kiểm tra sau sửa

| Kiểm tra | Kết quả | Giới hạn |
|---|---|---|
| `php artisan test --compact` | **35 tests passed, 177 assertions** | Suite mặc định; Breeze/profile tests không áp dụng được lưu lại nhưng excluded. Không chứng minh trực tiếp production database. |
| `vendor/bin/pint` trên PHP thay đổi | Hoàn thành; một số file đã được định dạng (`UserController`, `DashboardController`, baseline command, demo seeder) | Chỉ formatting các file PHP nêu trong lệnh; không chạy formatter toàn repository. |
| `php artisan view:cache` | Thành công | Compile templates, không thay thế walkthrough mọi trạng thái. |
| `npm run build -- --outDir <Temp>` | Thành công, Vite 8.3.1 | Output nằm ngoài repository; không sửa `public/build`. |
| Migrations fresh + seed trên DB cô lập | Thành công | Chạy trên database review riêng, không phải DB demo. |
| Baseline rồi migrate upgrade trên DB do chính migrations tạo | Thành công; migration `rent_amount` áp dụng và seed invoice còn nguyên | Bản sao DDL khớp DB demo trên MariaDB 10.4 từng thất bại `1041 Out of memory` khi ALTER `invoices`; do đó upgrade cho đúng schema/engine đang triển khai **chưa được chứng minh**. Không áp dụng lên DB demo. |
| Baseline dry-run trên DB demo | Thành công; nhận diện 10 migration cũ, không ghi bảng/dữ liệu | Chỉ dry-run read-only; DB mẫu hiện không có bảng `migrations`. |
| Browser desktop/mobile | Login admin trên ứng dụng test local mở dashboard và hiển thị dữ liệu seed; ở viewport 390×844, trang login và dashboard không tràn ngang; navbar collapse mở đúng sau tap. Login viewport mobile có card rộng 358px bên trong viewport 390px. | Walkthrough giới hạn ở admin dashboard/login và navbar; chưa walkthrough mọi module, form lỗi, empty state hoặc manager trên màn hình nhỏ. |

### Còn thiếu / cần làm rõ

- Migration upgrade trên bản sao đúng DDL thật chưa thành công trên MariaDB 10.4 do lỗi tài nguyên 1041. Cần staging MariaDB tương thích với tài nguyên gần production hoặc xác nhận phương án nâng cấp được DBA; không chạy thử thêm trên DB demo.
- Không có pagination trên property user list tìm kiếm; property list giữ pagination. Search/pagination module nghiệp vụ đã thêm trong source và có feature tests trên SQLite, nhưng chưa walkthrough đầy đủ dữ liệu lớn.
- Full default suite nay xanh sau khi loại Breeze/profile specs khỏi mặc định; nếu muốn báo cáo toàn bộ file test thì có thể chạy riêng các test scaffold và ghi rõ chúng đòi route/chức năng không có trong sản phẩm.
- Chưa kiểm tra báo cáo cuối (CLO1/CLO5), ERD/Use Case/hồ sơ nhóm, và chưa xác nhận với giảng viên cách diễn giải role `User` trong mục 3.2.
- Chưa walkthrough trực quan mọi module, trạng thái rỗng/validation/flash cho từng form, role manager mobile; test request đã xác nhận một số validation/flash trên DB test riêng.

### Trạng thái 6 nhóm theo yêu cầu

| Hạng mục | Trạng thái hiện tại |
|---|---|
| DB sạch, dữ liệu demo và baseline | Đạt trên môi trường test dựng mới; baseline dry-run trên DB mẫu thành công; upgrade trên DDL thật còn giới hạn do MariaDB 1041. |
| Validation/flash và test | Có feature tests nhiều module; suite mặc định 35/35 đạt. Coverage không bao quát mọi nhánh và request của mọi CRUD. |
| Role manager / khu `/admin` | README mô tả manager là vai trò User nội bộ; khu admin thống nhất tại `/admin`, property admin tại `/admin/properties`. Ý nghĩa role theo giảng viên vẫn cần xác nhận. |
| Pagination | Room/tenant/contract/invoice có pagination và giữ từ khóa; test có assertion tương ứng. |
| PHPUnit scaffold | Test mặc định xanh; Breeze/profile scaffold được giữ lại và excluded, vì route profile/password reset không thuộc app. |
| Responsive/UX | Login và admin dashboard đã kiểm tra tại viewport mobile; không tràn ngang, navbar mở được. Các màn hình còn lại chưa được audit trực quan đầy đủ. |



