# Review chức năng — Đồ án Quản lý phòng trọ

> **Cập nhật bổ sung:** Đã kiểm thử runtime bằng các tài khoản admin/manager do người dùng cung cấp. Xem mục “Addendum — kiểm tra bằng tài khoản được cung cấp” ở cuối; mục đó thay thế các nhận định trước đó về việc chưa có tài khoản hoặc chưa xác minh login/role.

**Ngày kiểm tra:** 22/10/2026  
**Phạm vi:** Source hiện tại sau đợt sửa gần nhất; HTTP GET tới ứng dụng local; login/logout bằng bốn tài khoản do người dùng cung cấp; route list; test chọn lọc `ExampleTest`.  
**An toàn dữ liệu:** Chỉ POST login/logout để kiểm thử session; không gửi POST/PUT/PATCH/DELETE cho thao tác nghiệp vụ, không chạy migration, không sửa `.env`, tài khoản hay database demo. Session driver là `file`, không ghi MySQL.

## Kết luận ngắn

Login/logout và quyền GET đã được kiểm chứng bằng bốn tài khoản được cung cấp. Các thao tác CRUD và cập nhật username vẫn **Chưa kiểm chứng** để bảo vệ database demo. Đây không phải kết luận các luồng đó bị lỗi.

Đã xác minh `/login` và `/` trả HTTP 200; login/logout bốn tài khoản; role redirect; quyền admin/manager; chặn truy cập chéo property; và trang chi tiết hợp đồng/hóa đơn với record mẫu. Guest vẫn bị chuyển hướng khỏi các route được bảo vệ. `ExampleTest` đạt 2/2 nhưng chỉ kiểm tra root. Các kết quả GET không chứng minh thao tác ghi CRUD.

Trong source có các điểm cần ưu tiên xác nhận: các danh sách room/tenant/contract/invoice lấy toàn bộ kết quả bằng `get()` nên không phân trang; hóa đơn được tính tổng bằng điện + nước + phí dịch vụ, trong khi biểu mẫu còn có trường tổng cho phép nhập nhưng server tự tính lại, và schema/UI chi tiết có `other_fee` nhưng form/controller không nhận trường này. Cần chốt công thức với yêu cầu nghiệp vụ trước khi coi đây là lỗi tính tiền.

## Kết quả từng phạm vi kiểm tra

Trạng thái dùng trong báo cáo: **Đạt trong kiểm tra thực tế** = có thao tác runtime cụ thể thành công; **Có trong source, chưa chạy nghiệp vụ** = code thể hiện xử lý nhưng chưa xác minh qua request hợp lệ; **Chưa kiểm chứng** = không đủ quyền/dữ liệu hoặc phép thử sẽ ghi database; **Phát hiện source-level** = xác định trực tiếp từ code, chưa phải lỗi runtime tái hiện.

| Phạm vi / tiêu chí liên quan | Kết quả | Bằng chứng thực tế hoặc source | Cách tái hiện / phần cần kiểm tra tiếp |
|---|---|---|---|
| Trang đăng nhập — CLO2/CLO4 | Đạt trong kiểm tra thực tế | `GET /login` trả HTTP 200. Đăng nhập bằng cả bốn tài khoản trả HTTP 200 sau redirect; admin đến `/dashboard`, managers đến dashboard property 1/2/3. `AuthController` (`app/Http/Controllers/AuthController.php:10-58`). | Chưa thử credential sai hoặc manager không có property được gán. |
| Đăng xuất — CLO4 | Đạt trong kiểm tra thực tế | Cả bốn POST `/logout` trả HTTP 200 sau redirect đến `/login`; GET `/dashboard` sau logout kết thúc ở `/login` (`AuthController.php:64-72`, `routes/web.php:107-108`). | Lần thử đầu dùng CSRF token cũ và nhận 419; lấy token mới rồi thử lại thành công. |
| Admin/manager — CLO4 | Đạt cho route GET đã thử | Admin vào `/admin/users`, `/properties`, dashboard và cả property 1–3: 200. Ba manager được chuyển lần lượt tới property 1/2/3; module property được gán: 200; `/admin/users` và property khác: 403. Middleware (`AdminMiddleware.php:11-25`, `PropertyAccess.php:11-35`). | Chưa thử role không hợp lệ hoặc manager không có `property_id`; không thử thao tác ghi. |
| CRUD khu trọ — CLO4 | Có trong source, chưa chạy CRUD | `PropertyController` có index/store/edit/update/destroy; create/update dùng transaction và validate (`PropertyController.php:16-41,61-111,116-196`). Route resource nằm trong auth + admin (`routes/web.php:23-31`). | Không tạo/sửa/xóa bản ghi. Chưa kiểm tra validation, liên kết manager, thông báo thành công hay chặn xóa khi còn dữ liệu. Thực hiện trên DB riêng, tạo bộ dữ liệu disposable. |
| CRUD phòng — CLO4 | Có trong source, chưa chạy CRUD | `RoomController` triển khai list/create/store/edit/update/destroy; các thao tác gắn property và edit/update/delete so `room.property_id` (`RoomController.php:11-109`). | Không gửi form. Chưa kiểm tra unique, validation lỗi, flash, hoặc thao tác xóa phòng có hợp đồng trên DB. |
| CRUD người thuê — CLO4 | Có trong source, chưa chạy CRUD | `TenantController` có CRUD; tenant được tạo qua `$property->tenants()` và edit/update/delete kiểm tra property (`TenantController.php:11-113`). | Không gửi form. Cần thử CCCD trùng, field thiếu/không hợp lệ, cùng property và property khác trong DB riêng. |
| CRUD hợp đồng — CLO4 | Có trong source, chưa chạy CRUD | `ContractController` dùng transaction/row lock; kiểm tra room và tenant thuộc property, đồng bộ trạng thái phòng và chặn hợp đồng hiệu lực trùng phòng (`ContractController.php:60-126,168-247,255-275`). | Chưa kiểm thử tạo/cập nhật/hủy/xóa, cạnh tranh trạng thái hoặc flash. Các luồng có ghi hợp đồng/phòng nên chỉ thử DB riêng. |
| CRUD hóa đơn — CLO4 | Có trong source; có điểm cần xác minh công thức | `InvoiceController` có CRUD, validate tháng/số tiền/trạng thái, kiểm tra hợp đồng thuộc property (`InvoiceController.php:53-100,145-188,192-203`). | Chưa gửi POST/PUT/DELETE nên chưa xác minh CRUD, lỗi trùng tháng, validation hay flash. Xem chi tiết công thức ở phần dưới. |
| Sửa tài khoản: username không đổi / username mới / username trùng — CLO4 | Chưa kiểm chứng | `UserController::update()` hiện dùng `Rule::unique('users','username')->ignore($user->id)` (`UserController.php:104-143`). Route PUT `/admin/users/{user}` được bảo vệ bằng auth + admin (`routes/web.php:43-55`). | Cả ba trường hợp đều cần truy vấn validate và trường hợp thành công còn ghi user. Chỉ thử trong DB riêng: giữ nguyên username; đổi sang tên duy nhất; đổi sang tên đã thuộc tài khoản khác. Không tạo/chỉnh account trong DB demo. |
| Chi tiết hợp đồng/hóa đơn — CLO2/CLO4 | Đạt với bản ghi mẫu đã thử | Admin xem detail contract/invoice hợp lệ ở property 1–3: 200; từng manager xem bản ghi thuộc property mình: 200. Controller kiểm tra property và nạp quan hệ (`ContractController.php:130-146`, `InvoiceController.php:103-121`). | Chưa thử empty state hoặc bản ghi thiếu quan hệ. |
| Truy cập chéo property — CLO4 | Đạt trong các phép thử GET đã thực hiện | Mỗi manager gọi rooms ở property khác nhận 403. Contract/invoice ID thuộc khu khác khi đặt dưới route property manager đang quản lý nhận 404; record thuộc khu mình nhận 200. | Chưa thử POST/PUT/DELETE chéo property vì không thay đổi database demo. |
| Validation / flash message — CLO2/CLO4 | Có trong source, chưa xác minh response | Các controller gọi `$request->validate()`; form nhiều nơi có `@error`; thao tác hợp lệ trả `with('success', ...)`, danh sách hiển thị session flash (ví dụ `RoomController.php:33-52`, `TenantController.php:38-57`, view index tương ứng). | Chưa gửi dữ liệu sai/đúng. Cần kiểm tra status/redirect, field error, old input và flash sau thao tác trong DB riêng. |
| Tìm kiếm, phân trang, giữ từ khóa — CLO4 | Đạt một phần trong source; chưa xác minh dữ liệu trả về | Property lọc theo `keyword`/status và dùng `paginate(10)->withQueryString()` (`PropertyController.php:16-39`); view hiển thị paginator (`properties/index.blade.php:188`). Room/tenant/contract/invoice có điều kiện search nhưng kết thúc bằng `get()` (`RoomController.php:11-25`, `TenantController.php:11-28`, `ContractController.php:14-45`, `InvoiceController.php:12-49`). User list paginate nhưng không có search (`UserController.php:13-19`). | Property là danh sách duy nhất có đủ search + pagination + giữ query trong source. Chưa kiểm tra kết quả thực tế vì cần dữ liệu đăng nhập. Phân trang các danh sách nghiệp vụ khác còn thiếu nếu tiêu chí áp dụng cho từng danh sách. |
| Quy tắc tổng tiền hóa đơn — CLO4 | Phát hiện source-level; nghiệp vụ kỳ vọng chưa đủ dữ kiện | `store` và `update` tính `total = electricity + water + service_fee` (`InvoiceController.php:81-85,170-174`). Form create/edit có input `total`, nhưng controller không validate/lưu giá trị gửi lên (`resources/views/invoices/create.blade.php:130-145`, `edit.blade.php:135-150`). View chi tiết hiển thị `other_fee` (`invoices/show.blade.php:53`), nhưng create/edit/controller không nhận trường này. | Có thể xác nhận phép tính thực tế bằng POST trong DB riêng. Cần hỏi/đối chiếu công thức trong yêu cầu nghiệp vụ: tiền thuê tháng có nằm trong hóa đơn không, `other_fee` có được tính không, total có phải server-computed? Không tự kết luận công thức sai khi chưa có đặc tả. |
| Route sửa gần nhất — root, invoice resource, show | Đạt trong phạm vi route/HTTP đã kiểm tra | `GET /` trả 200; `GET /login` trả 200. Các route invoice/contracts hiện chỉ ở dạng `/properties/{property}/...`; route list có đủ 7 action resource mỗi nhóm. Guest GET dashboard/admin/property/contracts show/invoice show trả 302. `ExampleTest`: 2 passed, 2 assertions; test này kiểm tra response `/` 200 (`tests/Feature/ExampleTest.php`). | `/` trả 200 không thay thế kiểm tra login hoặc luồng nghiệp vụ. Chưa thử route show đã render với bản ghi hợp lệ do thiếu auth. |

## Yêu cầu chấm điểm liên quan

- **CLO2 — Giao diện:** login và root được phục vụ; chưa walkthrough giao diện sau login, lỗi validation, flash sau thao tác, responsive hoặc dữ liệu chi tiết.
- **CLO4 — Sản phẩm/chức năng:** source có CRUD, auth, role, validation, search; runtime chỉ xác minh guest redirect, trang GET cơ bản và route root. CRUD/auth/role/property scope chưa được xác minh end-to-end.
- Theo rubric đã đọc ở các review trước, tìm kiếm + phân trang là yêu cầu chức năng. Trong source, chỉ danh sách property có đủ phân trang và giữ query; các danh sách phòng/người thuê/hợp đồng/hóa đơn chưa paginate.

## Kiểm tra đã thực hiện và giới hạn

1. Gửi GET tới `http://127.0.0.1:8000`: `/login` = 200; `/` = 200; `/dashboard`, `/admin/users`, `/properties/1/rooms`, `/properties/1/tenants`, `/properties/1/contracts`, `/properties/1/invoices`, `/properties/1/contracts/1`, `/properties/1/invoices/1` = 302 khi không có session đăng nhập.
2. `php artisan test --filter=ExampleTest --do-not-cache-result`: **2 tests passed, 2 assertions**. Test chỉ kiểm tra root trả 200; không kiểm tra DB hoặc nghiệp vụ CRUD.
3. Đọc route/controller/middleware/form/view hiện hành và kiểm tra danh sách route. Không chạy full PHPUnit: cấu hình `phpunit.xml` dùng SQLite `:memory:` và suite feature trước đây lỗi ở migration thiếu bảng `rooms`; chạy suite đó không giúp kiểm chứng CRUD với DB demo và không có môi trường test tách biệt được xác nhận trong lượt này.
4. Đăng nhập/đăng xuất có gửi POST session-only. Cấu hình `SESSION_DRIVER=file`; không có POST/PUT/PATCH/DELETE nghiệp vụ, không ghi dữ liệu DB demo và không chạy migration. Một lượt logout với CSRF token cũ trả 419; sau khi lấy token mới, logout thành công. Tám session file xác thực do hai lượt thử lỗi tạo ra đã được xác định và dọn.

## Thiếu sót theo mức ưu tiên và bước tiếp theo

### P0 — Chốt nghiệp vụ hóa đơn và xác minh tính tiền

Đối chiếu đề tài/bảng mẫu với công thức tổng tiền. Dựng database test riêng, chạy thử các giá trị xác định trước và xác minh total được tính ở server; kiểm tra tương quan giữa trường `other_fee`, `monthly_rent`, input `total` và số tiền lưu. Không thử trên DB demo.

### P1 — Có môi trường và tài khoản test cách ly

Các account admin/manager đã được dùng để xác minh đăng nhập, role và GET trên database hiện tại. Trước khi kiểm thử CRUD, validation sau POST hoặc username update, chuẩn bị DB test riêng/bản sao đã ẩn danh và dùng cùng account role ở môi trường đó; không ghi dữ liệu nghiệp vụ vào DB demo.

### P1 — Hoàn thiện search/pagination cho danh sách nghiệp vụ

Nếu rubric yêu cầu trên các danh sách chính, thêm paginate và giữ query string cho room/tenant/contract/invoice; sau đó kiểm tra keyword còn nguyên khi bấm trang tiếp theo. Property hiện đã có phần này trong source.

### P2 — Chạy walkthrough UI sau khi có tài khoản test

Mở giao diện mỗi role, kiểm tra trạng thái rỗng, kết quả lọc, lỗi field, flash, trang show và màn hình nhỏ; ghi lại bằng ảnh có thể dùng trong hồ sơ báo cáo.

## Ma trận kết luận cuối

### A. Đã xác minh thành công

- `GET /` và `GET /login` trả 200.
- Guest bị chuyển hướng 302 tại các route cần đăng nhập đã nêu.
- Route invoice/contract được lồng theo property và có action show; test root đạt 2/2.

### B. Có trong source nhưng chưa xác minh hành vi

- Login/logout, role redirect, admin/manager scope đã xác minh cho các account/routes GET trong Addendum; credential sai và role không hợp lệ chưa thử.
- CRUD khu trọ/phòng/người thuê/hợp đồng/hóa đơn; validation và flash.
- Cập nhật username giữ nguyên, đổi mới hợp lệ và báo trùng.
- Trang chi tiết mẫu và chặn truy cập chéo property đã xác minh bằng GET; chưa thử request ghi chéo property.
- Kết quả tìm kiếm thực tế và giữ keyword; công thức tổng hóa đơn đúng nghiệp vụ.

### C. Thiếu hoặc cần làm rõ trước khi nộp

- Pagination cho danh sách room, tenant, contract và invoice nếu yêu cầu áp dụng cho tất cả danh sách chính.
- Xác nhận cách tính hóa đơn, đặc biệt tiền thuê, `other_fee` và trường nhập `total`.
- Kiểm thử CRUD, validation/flash sau POST, username update và công thức invoice trên môi trường không phải DB demo.

## Addendum — kiểm tra bằng tài khoản được cung cấp

**Phần này cập nhật và thay thế các kết luận ở trên nói rằng chưa có tài khoản thử hoặc chưa xác minh login/role/property scope/logout/show runtime.** Các mật khẩu không được ghi vào báo cáo.

### Thao tác và kết quả

| Account | Kết quả login | Quyền/trang đã mở | Kết quả logout |
|---|---|---|---|
| `admin` | HTTP 200, chuyển `/dashboard` | `/admin/users`, `/properties`, dashboard và danh sách phòng của property 1–3: 200. Chi tiết hợp đồng/hóa đơn hợp lệ của property 1–3: 200. | POST logout trả 200, chuyển `/login`; truy cập `/dashboard` sau logout kết thúc ở `/login`. |
| `manager_q1` | HTTP 200, chuyển `/properties/1/dashboard` | Property 1 dashboard/rooms/tenants/contracts/invoices và chi tiết contract 1, invoice 1: 200. `/admin/users` và rooms property 2: 403. Contract/invoice ID của property khác gọi dưới property 1: 404. | POST logout trả 200, chuyển `/login`; truy cập dashboard sau logout kết thúc ở `/login`. |
| `manager_td` | HTTP 200, chuyển `/properties/2/dashboard` | Property 2 dashboard/rooms/tenants/contracts/invoices và chi tiết contract 3, invoice 3: 200. `/admin/users` và rooms property 3: 403. Contract/invoice ID của property khác gọi dưới property 2: 404. | POST logout trả 200, chuyển `/login`; truy cập dashboard sau logout kết thúc ở `/login`. |
| `manager_bt` | HTTP 200, chuyển `/properties/3/dashboard` | Property 3 dashboard/rooms/tenants/contracts/invoices và chi tiết contract 5, invoice 5: 200. `/admin/users` và rooms property 1: 403. Contract/invoice ID của property khác gọi dưới property 3: 404. | POST logout trả 200, chuyển `/login`; truy cập dashboard sau logout kết thúc ở `/login`. |

### An toàn và giới hạn

- Đã xác nhận `.env` đặt `SESSION_DRIVER=file`. Login/logout chỉ ghi session file; không ghi database. Các lượt kiểm tra khác chỉ là GET. Không gửi thao tác CRUD, không chạy migration, không thay đổi account hay `.env`.
- Một lượt thử đầu dùng CSRF token cũ sau khi Laravel xoay session, nên logout trả 419. Đã lấy token mới từ trang đã đăng nhập, logout thành công cả bốn tài khoản. Tám file session xác thực do hai lượt thử lỗi tạo ra đã được nhận diện theo nội dung auth session và xóa khỏi `storage/framework/sessions`; database không bị tác động.
- Các kết quả 200 chứng minh endpoint đã render với account và record mẫu tương ứng. Chúng không xác minh ghi/sửa/xóa, validation qua POST, flash sau lưu, các tình huống dữ liệu rỗng hoặc toàn bộ nghiệp vụ.
- CRUD, ba tình huống username (giữ nguyên/đổi hợp lệ/trùng), validation và flash sau POST vẫn cần kiểm tra trên database riêng để không thay đổi dữ liệu demo. Các kết luận về search/pagination và công thức invoice là source-level như đã nêu.

### Cập nhật ma trận kết luận

**Đã xác minh runtime:** login/logout và role redirect của bốn tài khoản; admin truy cập được toàn bộ property; manager chỉ vào được property được gán; admin route bị manager từ chối (403); cross-property route bị từ chối (403); object contract/invoice thuộc property khác bị từ chối (404); chi tiết hợp lệ render được (200).

**Còn chưa xác minh:** CRUD ghi dữ liệu, cập nhật username với ba trường hợp, validation/flash sau POST, quy tắc nghiệp vụ tổng invoice và search/pagination trên kết quả dữ liệu thực tế.
