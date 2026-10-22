# Kết quả hoàn thiện nghiệp vụ hóa đơn

**Ngày:** 22/10/2026  
**Phạm vi:** Công thức đã được chủ đồ án chốt; thay đổi source/migrations/tests liên quan hóa đơn. Không thay đổi `.env`, tài khoản hay dữ liệu DB mẫu.

## 1. Kết quả tổng quan

Đã triển khai công thức chính thức trong `store()` và `update()`; server tự tính tổng, `other_fee` được nhập/validate/lưu/hiển thị, tiền thuê được snapshot vào invoice khi tạo. Hóa đơn lịch sử chưa có snapshot không bị tự backfill hay tự tính lại. Hóa đơn đã thanh toán được khóa sửa/xóa.

Migration dựng schema sạch chạy thành công trên MySQL test riêng. **Chưa xác nhận được migration snapshot trên bản sao DDL khớp DB thật:** MariaDB 10.4.13 trả lỗi `1041 Out of memory` khi thêm cột vào bảng `invoices` có bản ghi. Lỗi vẫn xảy ra khi bỏ tùy chọn vị trí cột và khi yêu cầu `ALGORITHM=INSTANT`. Không áp dụng migration lên DB mẫu; cần xử lý giới hạn môi trường/triển khai trước khi chạy migration ở nơi đang có dữ liệu.

## 2. File đã thay đổi

| File | Thay đổi |
|---|---|
| `app/Http/Controllers/InvoiceController.php` | Dùng chung validation và công thức tiền; lấy contract trong property từ quan hệ route; kiểm tra tháng trùng; lưu rent snapshot; dùng số nguyên đơn vị cent để cộng chính xác; bỏ qua `total` từ request; giữ nguyên contract/tháng khi sửa; chặn sửa/xóa invoice paid hoặc legacy chưa đối soát. |
| `app/Models/Invoice.php` | Thêm factory, `rent_amount` vào fillable và decimal cast. |
| `app/Models/Contract.php`, `Property.php`, `Room.php`, `Tenant.php`, `User.php` | Thêm factory support để tạo fixture hợp lệ cho feature tests; không đổi quan hệ/nghiệp vụ các model này. |
| `resources/views/invoices/create.blade.php` | Thêm `other_fee`, hiển thị rent theo contract, preview tổng tự động, không còn input có thể sửa/gửi `total`; lỗi validation hiển thị tại trường. |
| `resources/views/invoices/edit.blade.php` | Hiển thị rent snapshot (không lấy rent hiện tại), cho sửa các khoản phí, preview tổng; contract/tháng bất biến; cảnh báo và khóa form cho legacy/paid invoice. |
| `resources/views/invoices/index.blade.php`, `show.blade.php` | Hiển thị đủ rent snapshot, điện, nước, dịch vụ, phí khác và total; tiền có hai chữ số thập phân; rent legacy hiện “cần đối soát”. |
| `database/migrations/2006_07_03_080800_add_property_fields.php` | Bổ sung các cột property mà ứng dụng hiện dùng vào chuỗi dựng mới. |
| `database/migrations/2006_07_03_080820_create_rental_core_tables.php` | Tạo schema nền rooms, tenants, contracts, invoices theo cấu trúc thực tế để chuỗi migration sạch dựng được. |
| `database/migrations/2006_07_03_080900_add_user_account_fields.php` | Bổ sung username/role cho schema users trong quy trình dựng mới. |
| `database/migrations/2006_07_03_081800_finalize_property_scopes.php` | Hoàn thiện ràng buộc/index property–room–tenant–contract cho schema dựng mới. |
| `database/migrations/2026_10_22_000000_add_rent_amount_snapshot_to_invoices.php` | Thêm `rent_amount DECIMAL(12,2) NULL`, không backfill dữ liệu cũ. |
| `database/factories/UserFactory.php`, `PropertyFactory.php`, `RoomFactory.php`, `TenantFactory.php`, `ContractFactory.php`, `InvoiceFactory.php` | Tạo dữ liệu test phù hợp schema và role. |
| `tests/Feature/InvoiceBillingTest.php` | 17 kiểm thử feature cho công thức, snapshot, validation, duplicate, property scope, legacy/paid policy, flash, biểu mẫu, chi tiết và request login/create/update/view. |

Không sửa các migration cũ. Các migration mới có timestamp xen giữa chuỗi migration cũ để các bảng nền tồn tại trước migration thêm `property_id`.

## 3. Database và schema

- `.env` đang trỏ tới MySQL database `quanlyphongtro`. Cột tiền thực tế của contract/invoice dùng `DECIMAL(12,2)`; `contracts.monthly_rent` là `NOT NULL`; các khoản invoice và `total` là `NOT NULL DEFAULT 0.00`; invoice có FK contract và unique `(contract_id, month)`.
- Trước/sau lượt làm việc, DB mẫu có 6 hóa đơn, 3 hóa đơn paid, `SUM(other_fee)=0.00`, `SUM(total)=3,190,000.00`; không có cột `rent_amount` trước migration. Truy vấn cuối xác nhận các số này không đổi.
- Migration mới khai báo snapshot nullable đúng precision `DECIMAL(12,2)`. Không có bản ghi nào trong DB mẫu được cập nhật.
- Đã chạy toàn bộ migrations trên `quanlyphongtro_invoice_test_20261022`, database test riêng được xác nhận rỗng trước `migrate:fresh`; tạo được 14 bảng và migration snapshot.
- Đã tạo các DB test riêng sau; không chứa dữ liệu DB mẫu:
  - `quanlyphongtro_invoice_test_20261022`: schema hoàn chỉnh; test tự rollback fixture, hiện có 0 invoice.
  - `quanlyphongtro_invoice_upgrade_20261022`: một fixture invoice giả lập từ test rollback/reapply, dùng để kiểm tra migration trên schema source.
  - `quanlyphongtro_invoice_upgrade_actual_20261022`: chỉ clone DDL của 5 bảng property/room/tenant/contract/invoice từ DB mẫu, không copy row; có 1 invoice giả lập paid để thử migration snapshot.
- Migration đổi schema trên DB nâng cấp riêng chạy lỗi `1041 Out of memory`; kiểm tra sau lỗi cho thấy row fixture (`total=69.00`, `other_fee=9.00`) còn nguyên, chưa có cột snapshot và không ghi migration record. Không chạy migration trên DB mẫu.
- DB mẫu hiện không có bảng `migrations`. Vì vậy không chạy `artisan migrate` trên DB mẫu: Laravel có thể bắt đầu từ migration tạo bảng đã tồn tại. Cần lập kế hoạch baseline/backup riêng trước khi triển khai migrations vào database hiện hữu.

## 4. Chính sách hóa đơn lịch sử

- Không gán `contracts.monthly_rent` hiện tại cho hóa đơn cũ. Sau khi migration được áp dụng, snapshot legacy để `NULL`; UI đánh dấu cần đối soát; total cũ giữ nguyên.
- Legacy invoice `rent_amount IS NULL` bị chặn sửa/xóa để không thể âm thầm thay đổi total hoặc mất lịch sử trước đối soát.
- Invoice `Đã thanh toán` bị chặn sửa/xóa. Đây là chính sách bảo toàn dữ liệu được triển khai trong lượt này; điều chỉnh hóa đơn đã chốt cần quy trình điều chỉnh riêng.
- Invoice mới lấy `monthly_rent` từ hợp đồng thuộc property của route và lưu thành snapshot. Invoice update dùng snapshot đã lưu, không truy vấn lại rent hiện thời.
- Không có backfill. Muốn xác nhận hóa đơn cũ cần bằng chứng lịch sử giá thuê độc lập; không dùng giá hợp đồng hiện tại làm bằng chứng.

## 5. Công thức

```text
total = rent_amount + electricity + water + service_fee + other_fee
```

Store lấy `rent_amount` từ `contracts.monthly_rent` tại lúc tạo. Update dùng `invoices.rent_amount`. Bốn khoản còn lại được validate là số không âm, tối đa 2 chữ số thập phân và nằm trong giới hạn `DECIMAL(12,2)`. Tổng được cộng bằng integer cents, kiểm tra không vượt giới hạn cột total, rồi lưu dạng chuỗi decimal hai chữ số. `total` client gửi (kể cả giả mạo) không được dùng.

## 6. Kiểm thử đã chạy

Mọi lệnh ghi database được đặt process environment trỏ tới DB test. Không chạy migration/seed/test ghi lên DB mẫu.

| Lệnh/kiểm tra | Kết quả |
|---|---|
| `php artisan migrate:fresh --database=mysql --force --no-interaction` trên `quanlyphongtro_invoice_test_20261022` | Thành công; toàn bộ chuỗi migrations dựng schema sạch, gồm `rent_amount`. |
| `php artisan test --compact --filter=InvoiceBillingTest` | **17 passed, 85 assertions**. Có kiểm tra 0 đồng, rent-only, phí khác, decimal, forged total, validation âm/sai precision/thiếu field, contract khác property, duplicate tháng, update giữ snapshot, sửa giá hợp đồng sau lập invoice, manager scope, invoice paid/legacy, form/list/show, flash và HTTP login/create/update/detail. |
| `php artisan test --compact` | **42 tests: 21 passed, 19 failed, 2 errors, 114 assertions**. Invoice suite pass. Các test còn lỗi thuộc scaffold Breeze/profile: test email-login trong khi app dùng username, kỳ vọng logout redirect `/` trong khi app redirect `/login`, và các route email verification/password/profile không có trong route hiện tại. Không sửa các module này vì ngoài phạm vi. |
| `php artisan route:list --path=invoices` | Thành công; có 7 route, tất cả dưới `properties/{property}/invoices`. |
| HTTP feature requests cho create/edit/index/show | Đều render thành công trong 17 invoice tests; đây là Laravel HTTP test, không phải browser walkthrough ngoài trình duyệt. |
| `php artisan view:cache` | Thất bại ở Blade component scaffold `[input-label]` trong view auth/profile không thuộc invoice. Đã chạy `view:clear`; invoice views vẫn render trong feature tests. |
| `vendor/bin/pint --format=agent ...` | Thành công trên các PHP file đã sửa. |
| `php -l` các PHP file đã sửa | Tất cả pass. |
| Migration snapshot trên bản sao DDL thật | **Chưa đạt**: MariaDB 10.4.13 trả `1041 Out of memory` khi `ALTER TABLE invoices ADD rent_amount`. Thử cả thêm cột cuối bảng và `ALGORITHM=INSTANT`; cùng lỗi. Buffer pool quan sát được là 16 MiB; đây là dấu hiệu môi trường hạn chế, chưa chứng minh nguyên nhân gốc. |

Không build frontend vì không sửa asset frontend; preview được viết trực tiếp trong Blade và đã kiểm tra qua response HTML của test.

## 7. Runtime HTTP

Test `test_admin_can_log_in_create_update_and_view_invoice_over_http` xác thực tài khoản test qua route login, sau đó dùng cùng session test để tạo, xem, cập nhật và xem lại hóa đơn. Assert tổng sau tạo là `361.00`, sau update là `450.00`. Test riêng xác nhận manager được tạo trong property được phân công và nhận 403 khi truy cập property khác. Đây là request qua Laravel HTTP kernel trên DB test; chưa dùng browser GUI.

## 8. Bảo toàn dữ liệu và rollback

- Database được ghi: chỉ ba DB test có tên liệt kê ở mục 3. DB mẫu `quanlyphongtro` chỉ được đọc metadata/schema/count; không có insert/update/delete/migration.
- Không tạo backup DB mẫu vì không thực hiện thao tác ghi lên đó. Các test fixtures được Laravel rollback; hai DB nâng cấp còn fixture giả lập để có thể kiểm tra lại.
- Đã chạy rollback đúng một migration snapshot trên DB test `quanlyphongtro_invoice_upgrade_20261022`, sau đó chạy lại migration và xác nhận fixture cũ giữ nguyên `total`/`other_fee`, snapshot mới `NULL`.
- Rollback migration snapshot sẽ xóa cột `rent_amount`; nếu đã có invoice mới có snapshot thì down migration làm mất snapshot đó. Không rollback trên DB có dữ liệu thật nếu chưa backup.
- `.env` và tài khoản ứng dụng không được sửa. Không ghi credential vào báo cáo.

## 9. Vấn đề còn lại

1. **P0 — Chặn triển khai migration snapshot vào DB hiện hữu cho tới khi thử được trên môi trường có đủ tài nguyên.** Điều tra lỗi MariaDB 1041 trên bản sao; thực hiện backup có thể restore, thử migration và đối chiếu dữ liệu trước/sau trong staging. Không chạy migration lên DB mẫu hiện tại.
2. **P0 — Database hiện hữu thiếu migration history.** Cần baseline/import schema hoặc migration deployment plan riêng; không chạy toàn bộ `artisan migrate` lên database hiện tại khi chưa đánh dấu trạng thái migration an toàn.
3. **P1 — Bộ test mặc định còn lỗi ngoài invoice.** Nếu cần CI xanh, đồng bộ test scaffold auth/profile với route và phương thức đăng nhập thật; phần này chưa sửa.
4. **P1 — Toàn ứng dụng chưa `view:cache` được do `[input-label]`.** Cần xử lý scaffold view/component ngoài lượt hóa đơn trước release nếu production dùng compiled views.
5. **P2 — Hoá đơn paid bị khóa hoàn toàn;** nếu nghiệp vụ cần điều chỉnh sau thanh toán, thiết kế credit/debit adjustment/audit riêng thay vì sửa hóa đơn đã chốt.

## 10. Chạy lại

Dùng PHP Laragon và database test riêng. Xác nhận `DB_DATABASE` không phải `quanlyphongtro` trước mọi lệnh migration/test:

```powershell
$env:DB_CONNECTION = 'mysql'
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = '3306'
$env:DB_DATABASE = 'quanlyphongtro_invoice_test_20261022'
$env:DB_USERNAME = 'root'
$php = 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe'
& $php artisan migrate:fresh --database=mysql --force --no-interaction
& $php artisan test --compact --filter=InvoiceBillingTest
```

Full suite có thể chạy bằng `& $php artisan test --compact`; kết quả hiện tại gồm các lỗi scaffold liệt kê ở mục 6. Không chạy các lệnh trên với `DB_DATABASE=quanlyphongtro`.
