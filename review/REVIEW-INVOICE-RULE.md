# Rà soát quy tắc tổng tiền hóa đơn

**Ngày rà soát:** 22/10/2026  
**Phạm vi:** Đọc source, rubric, tài liệu dự án và metadata schema hiện tại. Không đọc bản ghi nghiệp vụ, không gửi request, không chạy migration/seed, không sửa source hoặc database.

## Kết luận

Chưa đủ căn cứ chọn công thức cuối cùng: tài liệu chính thức và project không nói hóa đơn có bao gồm tiền thuê tháng hay chỉ gồm khoản phát sinh.

Source hiện tính:

`total = electricity + water + service_fee`

khi tạo và cập nhật. `other_fee` không được tính dù trường này tồn tại trong schema/model và được hiển thị trên trang chi tiết. Form không cho nhập `other_fee`. Nếu phí khác là thành phần thu trên hóa đơn, đây là thiếu sót xác định được từ luồng hiện tại. Còn việc cộng `monthly_rent` phải chờ xác nhận.

## Trường dữ liệu và nguồn

| Trường | Nguồn/vị trí | Người dùng nhập / server tính | Được lưu | Hiển thị |
|---|---|---|---|---|
| `monthly_rent` | `contracts.monthly_rent`; model Contract cast `decimal:2` | Không có input hóa đơn; controller không đọc giá trị này. | Không có cột snapshot trên invoice. | Không hiển thị trên invoice show. |
| `electricity` | `invoices.electricity`; Invoice cast `decimal:2` | Nhập ở create/edit; validate required, numeric, min 0. | Có trong create/update payload. | Index và show. |
| `water` | `invoices.water`; Invoice cast `decimal:2` | Nhập ở create/edit; validate required, numeric, min 0. | Có trong create/update payload. | Index và show. |
| `service_fee` | `invoices.service_fee`; Invoice cast `decimal:2` | Nhập ở create/edit; validate required, numeric, min 0. | Có trong create/update payload. | Index và show. |
| `other_fee` | `invoices.other_fee`; Invoice fillable/cast `decimal:2` | Không có input và không có validation rule. | Không nằm trong payload của store/update. | Show hiển thị; index không có cột. |
| `total` | `invoices.total`; Invoice fillable/cast `decimal:2` | Form có input required, nhưng server tự tính; không validate giá trị gửi lên. | Lưu giá trị do controller tính. | Index và show. |
| `contract_id`, `month`, `status` | Invoice và quan hệ contract | Chọn/nhập; được validate. | Lưu khi tạo/cập nhật. | Tháng/trạng thái và thông tin hợp đồng được hiển thị. |

Metadata schema hiện tại (đọc qua Schema, không đọc giá trị bản ghi):

- `contracts`: `id, property_id, room_id, tenant_id, start_date, end_date, monthly_rent, deposit, status, notes, created_at, updated_at`.
- `invoices`: `id, contract_id, month, electricity, water, service_fee, other_fee, total, status, due_date, paid_at, notes, created_at, updated_at`.

Trong repository không có migration tạo bảng invoices/contracts. Migration contracts hiện có chỉ thêm `property_id` vào bảng contracts có sẵn: `database/migrations/2006_07_03_081404_add_property_id_to_contracts_table.php`. Vì vậy các cột tiền trong database hiện tại chưa được mô tả đầy đủ bằng migrations trong source.

## Theo dõi luồng dữ liệu

| Bước | Bằng chứng | Hành vi |
|---|---|---|
| Form tạo | `resources/views/invoices/create.blade.php:43-153` | Nhập contract, tháng, điện, nước, phí dịch vụ, total, trạng thái. Không có phí khác hoặc tiền thuê. |
| Form sửa | `resources/views/invoices/edit.blade.php:45-159` | Các trường tương tự create; có input total; không có phí khác. |
| Validate tạo | `app/Http/Controllers/InvoiceController.php:67-78` | Validate contract/month/electricity/water/service_fee/status. Không validate total, other_fee hoặc precision tiền cụ thể. |
| Tính và lưu tạo | `InvoiceController.php:81-99` | Cộng điện + nước + phí dịch vụ; lưu các khoản này và total. Bỏ qua other_fee. |
| Validate, tính và lưu sửa | `InvoiceController.php:146-188` | Lặp lại cùng rule và công thức; client total bị ghi đè bằng total do server tính. |
| Model | `app/Models/Invoice.php:12-34`; `app/Models/Contract.php:12-29` | Invoice có fillable/cast other_fee và total; Contract có monthly_rent. Model không tự định nghĩa công thức total. |
| Chi tiết | `resources/views/invoices/show.blade.php:40-57` | Hiển thị điện, nước, service_fee, other_fee và total; không hiển thị rent. |

Input `total` hiện là bắt buộc trong HTML nhưng không được controller sử dụng; đây là giao diện gây hiểu nhầm. Controller tính lại total là hướng an toàn hơn tin giá trị do trình duyệt gửi, nhưng form nên thể hiện total là giá trị tính tự động.

## Đối chiếu tài liệu

### Rubric chính thức

Đã đọc `tieuchi.docx`:

- Mục 3.3 yêu cầu CRUD trên các bảng quan hệ, auth/role, tìm kiếm + phân trang, validation và flash message.
- Mục 8 yêu cầu chức năng hoạt động đúng, không lỗi và dùng kỹ thuật Laravel phù hợp.
- Không có yêu cầu nào định nghĩa cách tính hóa đơn, việc cộng tiền thuê, phí khác, làm tròn hoặc thời điểm chốt giá.

Rubric yêu cầu tính toán đúng với nghiệp vụ nhưng bản thân không xác định nghiệp vụ đó là gì.

### Tài liệu nghiệp vụ khác

Trong project tìm thấy rubric, README mặc định Laravel, các báo cáo review và source. Không tìm thấy đề bài nghiệp vụ riêng, mẫu hóa đơn, tài liệu thu tiền hoặc SQL export quy định công thức. Báo cáo review trước ghi nhận được sự không nhất quán trong code, nhưng không phải đặc tả nghiệp vụ.

## Công thức còn có thể áp dụng

1. **Hóa đơn chỉ thu khoản phát sinh:**  
   `electricity + water + service_fee + other_fee`  
   Phù hợp nếu tiền thuê được thu riêng. Schema và trang show có `other_fee`, nhưng không có tài liệu xác nhận rent thu riêng hoặc xác nhận phí khác luôn được cộng.

2. **Hóa đơn là tổng tiền phải trả tháng đó:**  
   `monthly_rent + electricity + water + service_fee + other_fee`  
   Phù hợp nếu hóa đơn bao gồm tiền thuê. Không có tài liệu xác nhận hóa đơn có ý nghĩa này; invoices cũng không có cột snapshot rent.

3. **Hiện trạng trong source:**  
   `electricity + water + service_fee`  
   Đây là công thức code đang dùng, không phải căn cứ chứng minh đó là công thức nghiệp vụ đúng.

**Kết luận:** chưa chọn công thức 1 hay 2. Cần chủ đồ án/giảng viên xác nhận:
- Hóa đơn tháng có bao gồm tiền thuê không?
- `other_fee` có phải khoản thu được cộng vào total không?

Nếu `other_fee` được tính thì việc hiện tại không nhận/lưu/cộng nó là thiếu sót dù rent có được tính hay không. Nếu rent được tính, cần quyết định lưu snapshot tiền thuê vào invoice hay chấp nhận rằng hợp đồng có thể thay đổi giá sau khi lập hóa đơn; không có snapshot có thể làm nội dung hóa đơn cũ khó đối chiếu.

## Đề xuất sau khi xác nhận

Chưa đổi công thức trước khi có xác nhận. Trong cả hai nhánh, để server tính total và không tin total client gửi.

- Nếu rent không tính, other fee có tính: `total = electricity + water + service_fee + other_fee`.
- Nếu rent tính và other fee tính: `total = rent_amount_for_this_invoice + electricity + water + service_fee + other_fee`; cân nhắc lưu rent snapshot để bảo toàn lịch sử.
- Nếu other fee không tính: cần làm rõ vì sao trường này tồn tại trong schema/model/show; sau đó thống nhất việc giữ hay bỏ khỏi biểu diễn hóa đơn.
- Với các khoản được dùng, validate số không âm và thống nhất precision theo đơn vị tiền/schema.
- Bỏ input total tự do hoặc hiển thị readonly/preview; server vẫn là nguồn quyết định.

## Kế hoạch test trên database riêng

Không chạy các test dưới đây trong lượt này. Tạo DB test riêng đúng schema; không trỏ vào DB mẫu. Sau khi chủ đồ án xác nhận công thức, ghi expected total cụ thể cho từng case.

| Trường hợp | Dữ liệu | Kỳ vọng |
|---|---|---|
| Tất cả bằng 0 | Điện, nước, dịch vụ, phí khác = 0; hợp đồng có rent để phân biệt hai chính sách | Nếu rent tính thì total bằng rent; nếu không thì 0. |
| Có phí khác | other_fee dương, các khoản khác 0 | Nếu phí khác được xác nhận là khoản thu, total phải gồm phí này (và rent nếu policy yêu cầu). |
| Có tiền thuê | Contract có monthly_rent dương, các khoản invoice bằng 0 | Phân biệt rõ: total bằng rent nếu gồm rent; nếu thu riêng thì 0. |
| Số thập phân | Các khoản có phần lẻ hợp lệ theo precision | Total không bị sai do ép kiểu/precision. |
| Client giả mạo total | Gửi total khác xa tổng thành phần | Server tính lại, không lưu total client gửi. |
| Giá trị âm | Từng khoản âm lần lượt | Validation báo lỗi, không tạo/cập nhật invoice. |
| Không phải số / thiếu trường | Chuỗi chữ, null hoặc thiếu từng khoản | Validation chỉ rõ trường lỗi, DB test không bị thay đổi. Quyết định other_fee là required hay nullable/0. |
| Update parity | Sửa từng khoản ở hóa đơn có sẵn | Update dùng cùng công thức và cập nhật total nhất quán với create. |
| Giá hợp đồng đổi sau khi lập hóa đơn | Tạo invoice rồi đổi monthly_rent | Nếu invoice gồm rent, total lịch sử phải tuân theo chính sách snapshot đã chọn. |

Do lượt này chỉ đọc, không có phép thử tính tiền runtime hoặc thay đổi bản ghi nào được thực hiện.

## File có thể cần sửa ở lượt sau

Chỉ sửa sau khi nghiệp vụ được xác nhận:

1. `app/Http/Controllers/InvoiceController.php` — validate/lưu other_fee và/hoặc rent snapshot; tính cùng công thức trong store/update.
2. `resources/views/invoices/create.blade.php` và `edit.blade.php` — thêm phí khác nếu cần; hiển thị rent nếu tổng gồm rent; bỏ total input tự do hoặc đổi thành readonly/preview.
3. `resources/views/invoices/show.blade.php` và có thể `index.blade.php` — hiển thị các thành phần thực sự cấu thành tổng.
4. Migration mới nếu thêm rent snapshot hoặc cần quản lý schema invoice/contracts từ source. Không chạy migration trên DB demo.
5. `tests/Feature/InvoiceTest.php` (hoặc suite tương ứng) — kiểm tra công thức, validation, update parity và lịch sử trên DB riêng.

`app/Models/Invoice.php` đã có fillable/cast `other_fee`; có thể không cần sửa nếu không thêm snapshot. `Contract.php` đã có `monthly_rent`; nếu cần rent snapshot thì cần migration/model tương ứng.

