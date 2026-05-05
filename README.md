***🚀 วิธีการรัน API***

***หมายเหตุ*** สามารถข้ามไป login ได้เลยเนื่องจาก มี seed user อยู่แล้ว   
***หมายเหตุ*** API ตัวนี้รองรับ php version 8.4 ขึ้นไป หากรันด้วย xampp ที่ต่ำกว่า 8.4 ต้อง upgrate php version ในไฟล์ xampp ให้สามารถรันด้วย php version 8.4 ได้   
***หมายเหตุ*** รูปแบบการรันเป็นการรันผ่าน postman แบบ local ทั้งหมด   
### 1.register
โดยจัด body(raw) เป็นแบบตัวอย่าง โดยที่ password ต้องไม่ตำกว่า 8 ตัว และ role สามารถเลือกได้แแค่ public หรือ staff
```json
{
    "name": "ริวจินจิ",
    "email": "staff3@test.com",
    "password": "123456789",
    "password_confirmation": "123456789",
    "role": "staff"
}
```
### 2.login
การลงชื่อเข้าใช้ ต้อง ตั้งค่า ใน body(raw) ตามตัวอย่างได้เลย 
```json
{
    "email": "public@test.com",
    "password": "123456789"
}
```
***seed user public***
email    = public@test.com
password = 123456789

***seed user staff***
email    = staff@test.com
password = 123456789

3.การดึง token ไปใช้งานสำหรับ role ต่างๆ 
หลังจาก login เสร็จแล้ว นำ token ที่ได้ ในแต่่ละ role ไป ใส่ใน Authorization ใน Request ของแต่ละตัวตามต่อไปนี้

### public 
1.create cooperative (publlic)
2.get cooperative (publlic)

### staff
1.review (staff)
2.status check (staff)

***การทำงานในฝั่ง public***
### 1.create cooperative (publlic)
เป็น api ที่ไว้ใช้สำหรับสร้าง สหกรณ์โดยเงือนไขคือ จะต้องชื่อไม่ซ้ำ และ จำนวนสมาชิกไม่ต่ำว่า 10 คน ตามตัวอย่าง
{
    "name": "สหกรณ์บ้านหนองสำราญ",
    "member_count": 20
}

### 2.get cooperative (publlic)
เป็น api สำหรับรัน public รันดูว่าสหกรร์ที่ถูกร้องขอไปนั้น ถูกประเมินว่าอย่างไรอีกทั้งยังสามารถดูจำนวนที่สหกรณ์ที่ public ร้องขอไปทั้งหมด สามารถรันได้เลย

***การทำงานในฝั่ง public***
### 1.review (staff)
เป็น api สำหรับการประเมินสหกร์ โดยที่ ต้องรัน url เป็น id ของ สหกรณ์ที่เราต้องการจะตรวจสอบ ตามโครงสร้าง http://127.0.0.1:8000/api/admin/coop-requests/{id}/review
ตัวอย่าง

http://127.0.0.1:8000/api/admin/coop-requests/1/review

และใน body(raw) ต้อง set ค่า status สำหรับการประเมิน โดยมี 2 ค่า คือ approved และ rejected ไม่สามารถประเมินซ้ำได้
ตัวอย่าง
```json
{
    "status": "approved"
}
```
### 2.status check (staff)
เป็น api สำหรับรันดูสถานะสหกรณ์และจำนวนสหกรณ์ทั้งหมดได้ อีกทั้งยังสามารถ filter เฉพาะสถานะสหกรณ์นั้นๆได้ โดยหากรันทันที จะสามารถดูสถานะสหกรณ์และจำนวนทั้งหมดได้
การ filter จะต้อง ไป set ค่าใน body(raw) โดยค่าที่สามารถ filter ได้ มี pending approved และ rejected เท่านั้น ตามตัวอย่าง
```json
{
    "status": "rejected"
}
```
***การตั้งค่า env***

DB_CONNECTION=mysql   
DB_HOST=127.0.0.1   
DB_PORT=3306   
DB_DATABASE=testwork_db   
DB_USERNAME=root   
DB_PASSWORD=   

***คำสั่ง migrate***
### 1.สร้าง database ด้วยคำสั่ง php artisan migrate 
หากไม่มี database ที่มีชื่อ testwork_db เมื่อรันเสร็จแล้วจะขึ้นข้อความ 
The database 'testwork_db' does not exist on the 'mysql' connection. Would you like to create it?
ให้ตอบ yes เป็นการสร้าง database ที่ไม่มีอยู่เสร็จสิ้น

### 2.ดึง seed user ด้วยคำสั่ง php artisan db:seed
เป็นการดึง user seed ที่ set ไว้

### 3.ล้างข้อมูล และดึง seed หากข้อมูลที่รันมากเกินไปด้วยคำสั่ง php artisan migrate:fresh --seed
หากข้อมูลเยอะเกิดจากการรัน ก็สามารถรันเพื่อล้างข้อมูลได้ และดึง seed มาใช้ได้เลยไม่ต้อง  



