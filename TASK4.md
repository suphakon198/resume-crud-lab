คำตอบ Task 4 

ทำไม UPDATE และ DELETE ต้องมี WHERE:
WHERE ระบุว่าจะให้คำสั่งทำงานกับแถวไหน ถ้าไม่มี WHERE คำสั่งจะกระทบทุกแถวในตาราง เช่น DELETE FROM skill จะลบ skill ทั้งหมด และ UPDATE skill SET skill_level = 'Beginner' จะเปลี่ยนทุกแถว ในงานนี้ใช้ WHERE id = ? AND profile_id = ? เพื่อให้แก้หรือลบได้เฉพาะแถวที่เลือก และเป็นของ profile ที่กำหนดเท่านั้น

Parameter binding:
โค้ดเขียน SQL ด้วยตัวแทน ? แล้วใช้ mysqli_stmt_bind_param ส่งค่าแยกต่างหาก เช่น bind_param($stmt, 'ssii', $name, $level, $id, $pid) ทำให้ฐานข้อมูลถือว่าค่าที่ผู้ใช้กรอกเป็นข้อมูล ไม่ใช่ส่วนของคำสั่ง SQL จึงป้องกัน SQL injection ได้ (เช่น ถ้ากรอกชื่อ skill เป็น '; DROP TABLE skill; -- ก็จะถูกเก็บเป็นข้อความธรรมดา)