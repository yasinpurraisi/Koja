

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
    - status (`visible`, `hidden_by_admin`, or `hidden_by_suspension`)

- salon_opening_hours:
    - id `Primary Key`
    - salon_id `Foreign Key` to `salons.id`
    - day_of_week
    - opens_at
    - closes_at
    - is_closed

## Koja project explanation (interview guide)

### Short project summary

Koja is a server-rendered salon discovery and management platform. Customers can find approved salons, read reviews, and leave one review per salon. Salon owners can submit a listing and manage its details and opening hours. Administrators review salon submissions and moderate salons, users, and reviews.

### Technology and structure

- **Backend:** PHP 8.3+ and Laravel 13, with Eloquent models and Blade templates.
- **Routes:** `routes/web.php` defines the public, authentication, owner, and administrator web endpoints.
- **Controllers:** `app/Http/Controllers` validates/co-ordinates requests and returns views or redirects.
- **Actions:** `app/Actions` contains focused moderation operations such as approving a salon or suspending a user.
- **Models and policies:** `app/Models` describes persisted data and relationships; `app/Policies` decides who may perform protected operations.
- **Middleware:** `app/Http/Middleware` restricts routes by role and prevents suspended accounts from continuing to use protected pages.
- **Views:** `resources/views` contains the shared layout, page templates, and small reusable Blade partials.
- **Styling:** The shared layout loads `public/css/app.css` directly. `resources/css/app.css` is the separate Tailwind/Vite starter stylesheet; the main application pages do not load that file through the shared layout.
- **Data setup:** `database/migrations` defines the schema, `database/factories` builds test/demo records, and `database/seeders/DatabaseSeeder.php` creates sample users, salons, schedules, and reviews.

### Main request flows

1. **Discover salons:** `/` redirects to `/salons`. `SalonController@index` only lists approved salons, optionally filters by city or search text, computes visible-review counts and averages, and paginates the results. The salon directory and detail pages are rendered with Blade.
2. **Create an account and sign in:** `AuthController` validates a phone number and password, assigns new accounts the customer role, hashes passwords through the model cast, and regenerates the session after authentication.
3. **Register a salon:** An active customer submits a salon through `SalonRegistrationController`. The submission is created as pending and the user becomes an owner. An owner can have only one salon.
4. **Manage a salon:** Owners can maintain their salon information, opening schedule, and review feedback from the owner dashboard.
5. **Moderate the platform:** Admin-only routes use role middleware, policies, and moderation actions to approve/reject or suspend/restore salons, suspend/restore users, and hide/restore/delete reviews.
6. **Write reviews:** Active customers can review an approved salon other than their own. A database unique key prevents duplicate reviews by the same user for a salon; review status controls public visibility.

### Data model and rules

- A **User** has a role (`customer`, `owner`, or `admin`) and an account status (`active` or `suspended`). A user can own at most one salon, write multiple reviews, and may approve salon listings.
- A **Salon** belongs to an owner and can have many reviews and opening-hour records. Its lifecycle status is pending, approved, rejected, or suspended. Public pages expose only approved salons.
- A **Review** belongs to one user and one salon, has a 1–5 rating, optional comment, and a visibility/moderation status. The database enforces one review per user/salon pair.
- **Salon opening hours** store one record per salon and weekday. Weekdays use ISO numbering (Monday = 1 through Sunday = 7); closed days do not need opening or closing times.
- Foreign keys and cascade rules keep dependent reviews and opening hours consistent when a salon is deleted.

### Security and design points to explain

- Request data is validated before it is written; controller update paths select the permitted fields rather than trusting submitted role, status, or ownership values.
- Blade's escaped `{{ }}` output is used for user-controlled text, and state-changing forms include CSRF tokens.
- Authentication, active-account checks, role middleware, and policies serve different purposes: being signed in alone does not grant owner/admin permissions.
- Passwords are hashed by Laravel's `hashed` model cast. Login and registration POST routes are throttled.
- Salon approval/rejection and account suspension use database transactions where related records or state transitions must stay consistent.

### Interview walkthrough

For an end-to-end example, describe opening `/salons`: the route invokes `SalonController@index`; the controller builds the approved-salon query and applies optional filters, aggregates, and pagination; it returns `salons.index`; that Blade view renders each result through `partials.salon-card`; `layouts.app` provides the navigation, flash messages, footer, and shared stylesheet. For an owner action, explain how the route's authentication/role middleware is followed by policy authorization, validation, an Eloquent write, and a redirect with a flash message.

### Verified issues to address separately

- **Owner salon routes lack route model parameters:** `owner.salon.edit`, `owner.salon.update`, `owner.salon.destroy`, and `owner.salon.opening-hours.update` have no `{salon}` segment in `routes/web.php`, although their controller methods require `Salon $salon` and the forms generate URLs with a salon argument. These requests cannot bind the intended salon from the route as written, so owner edit/update/delete/opening-hours actions need a separate routing fix.
- **The existing feature smoke test is stale/failing:** `tests/Feature/ExampleTest.php` expects `GET /` to return 200, but `/` intentionally redirects to `/salons` and returns 302. The other existing test only checks that `true` is true, so the application currently has almost no behavior-specific test coverage.
- These application/test findings were documented but not changed during this styling-only task.
