// ارایشگر ها
// آدم ها لیست آرایشگر های شهر رو ببینند
// آدم ها به اون آرایشگر ها نمره داده باشند و کامنت داشته باشه
// پنل ادمین برای ارایشگر که ریتینگ هاشو ببینه

## Entities

- Users : 
    - id `Primary Key`
    - name
    - phone number `unique`
    - password
    - role
    - status

- Salons : 
    - id `Primary Key`
    - owner_id `Foreign Key` to `users.id`
    - name
    - description
    - phone number
    - city
    - address
    - status
    - approved_by
    - reviewed_at
    - latitude, longitude

- Reviews :
    - id `Primary Key`
    - user_id `Foreign Key` to `users.id`
    - salon_id `Foreign Key` to `salons.id`
    - rating
    - comment
    - visible   

- salon_opening_hours:
    - id `Primary Key`
    - salon_id `Foreign Key` to `salons.id`
    - day_of_week
    - open_at
    - close_at
    - is_closed
