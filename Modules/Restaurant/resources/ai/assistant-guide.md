# SajiloBhoj — Restaurant System Guide

This is everything the SajiloBhoj support assistant knows about the product. SajiloBhoj is a restaurant
management system from Nepal: each restaurant gets its own admin panel (at `/admin` on its own web address)
and its own public website with online ordering, a QR menu and table booking. Money is in Nepali rupees,
written "Nrs".

## Signing in and getting started
- Staff sign in at `/admin/login` on the restaurant's web address.
- A new restaurant signs up on its own web address (restaurant name, brand colour, owner name, email,
  password). The owner then gets a 6-digit code by email and enters it on the "Verify email" screen. The
  website and admin panel go live after that.
- After verifying, the owner chooses a plan. The first plan comes with a free trial.
- Every new restaurant starts with a ready website (all settings filled in with its name and colour),
  a starter food menu with photos, Table 1–4 in a "Main Hall" room, kitchen columns and default staff roles.
- Each person can change their own name, phone and password under **Profile** (`/admin/profile`).

## Admin pages (sidebar)
Which pages someone sees depends on their role's permissions and on the restaurant's plan.

### Dashboard (`/admin/dashboard`)
Today's sales, orders, table occupancy, kitchen queue, this month's revenue, average bill, new customers
and top-selling items. Everyone with a role lands here after signing in.

### Tables (`/admin/tables`) — Table Management
- Floor plan: rooms (e.g. "Main Hall", "Rooftop") with tables you can drag into place. Each table has a
  number, capacity (seats/chairs), description and photos.
- Table status: **available**, **occupied**, **reserved**, **cleaning**. Click a table to change it.
- Rooms: add, rename, add a photo, delete.
- Bookings: add a booking (name, phone, email, date, time, party size, notes) and set it to
  **pending**, **confirmed** or **cancelled**. Website visitors can also book from the public `/table` page;
  those arrive as pending bookings.

### Orders (`/admin/orders`)
- Track dine-in, takeaway and delivery orders. Filter by type and status.
- Order statuses: **pending** (new) → **confirmed** (accepted) → **preparing** (cooking) → **ready**
  (ready to serve) → **served**. An order can also be **cancelled**.
- Revenue counts **served** orders only.
- Orders come from staff (admin panel / Kitchen Display) and from the website checkout or table QR menu.
- Each order has an order number, items with quantity and price, subtotal, discount, tax and grand total.

### Kitchen Display (`/admin/kitchen`)
- A board of columns. Default columns: **New Orders** (pending), **Accepted** (confirmed),
  **Cooking** (preparing), **Ready to Serve** (ready).
- Drag an order card to another column, or click it, to move it along. Serving it removes it from the board.
- You can add, rename, recolour, reorder and delete columns, and add a kitchen order directly.

### Billing (`/admin/billing`)
Invoice Builder: build a bill, apply a discount, see the payment summary and recent bills, and export.

### Menu (`/admin/menu`) and Categories
- Menu items: name, description, price, photo, preparation time, availability (on/off switch) and order.
  Items switched off are hidden on the website.
- Categories group items (e.g. Momo, Drinks), each with a name, photo, description and order; categories
  can be switched off too.
- The plan sets how many menu items you can have (see Plans).
- **QR Code** tab (`/admin/menu?tab=qr`): a QR code for the whole menu or for one table. Choose "Link to",
  then Download or Print it. Guests scan it to open the menu (for a table: `/menu/{table}`) and order.

### Procurement
- **Suppliers** (`/admin/suppliers`): the companies you buy stock from (name, phone, email, address).
- **Inventory** (`/admin/inventory`): stock items in categories, each with a unit (kg, litre, pcs…),
  quantity, reorder level and cost price. An item is **low stock** when its quantity is at or below its
  reorder level.
- **Purchase Orders** (`/admin/purchase-orders`): order stock from a supplier (invoice number, items,
  quantity, unit price). Statuses: **pending**, **ordered**, **received**, **cancelled**. Setting a purchase
  order to received adds its quantities to inventory.

### Management
- **Reports** (`/admin/report`): this month's performance — revenue, orders and top items.
- **Customers** (`/admin/customers`): registered website customers, their orders and loyalty points.
  Customers earn **1 loyalty point for every Nrs 100** spent on website orders.
- **Users & Roles** (`/admin/user`): add staff (name, email, phone, role, password), switch staff on/off,
  and create roles. Each role chooses which sections it can open. The **Owner** role has full access and
  cannot be edited or deleted. Default roles:
  - Manager — everything except staff accounts and website settings.
  - Waiter — dashboard, orders, kitchen, tables.
  - Receptionist — dashboard, tables, orders, billing, customers.
  - Chef — dashboard, kitchen, menu, inventory.
  Sections a role can be given: Dashboard, Orders, Kitchen Display, Tables & Bookings, Billing, Customers,
  Menu, Inventory, Suppliers, Purchase Orders, Reports, Users & Roles, Website Settings. A section not in
  the current plan shows an "Upgrade" tag.
- **Balance & Subscription** (`/admin/subscription/top-up`): see Billing & plans below.
- **Settings** (`/admin/settings`): website settings in sections — **General** (restaurant name, tagline,
  currency, tax percent — 13% by default), **Theme** (brand colour used across the website and admin),
  **Homepage**, **About**, **Contact & Social**. **Navigation Menu** (`/admin/settings/navigation`) manages the links in
  the website header, mobile menu and footer.

## Public website (the restaurant's own web address)
- Home `/`, About `/about`, Menu `/menu`, Table booking `/table` (rooms and tables with photos),
  Contact `/contact`.
- Online ordering: guests add items to the cart and check out at `/checkout` (dine-in, takeaway or
  delivery). Orders appear in the admin Orders page and Kitchen Display as **pending**.
- Customers can register and sign in (`/register`, `/login`) to see their orders under `/account` and
  collect loyalty points.
- If the restaurant's subscription is paused, the website shows a "coming soon" page until it is topped up.

## Billing & plans (SajiloBhoj subscription)
- Plans (monthly price, trial):
  - **Starter** — Nrs 299/month, 30-day trial. Website & online ordering, dashboard, orders, kitchen,
    tables, menu, users, settings. Up to 25 menu items, 3 staff, 50 customers.
  - **Business** — Nrs 999/month, 30-day trial. Starter + billing, customers, reports.
    Up to 100 menu items, 10 staff, 200 customers.
  - **Professional** — Nrs 2,499/month, 30-day trial. Business + inventory, suppliers, purchase orders.
    Up to 500 menu items, 25 staff, 1,000 customers.
  - **Enterprise** — Nrs 4,999/month, no trial. Everything, no limits.
- Only a restaurant's very first plan gets the free trial. Change plan any time from Balance &
  Subscription → "Change" (`/admin/subscription/choose`); changing ends any running trial.
- Prepaid balance: after the trial, the plan's cost is taken from the balance by the hour
  (monthly price ÷ 720 hours). Per day = monthly price ÷ 30.
- Credit: when the balance runs out, the restaurant can go down to **Nrs -100** before the plan pauses.
  When paused, the admin panel only shows the top-up page and the website shows "coming soon".
- Top up on Balance & Subscription with **Khalti** or **eSewa** (minimum Nrs 100, maximum Nrs 100,000).
  Quick amounts are 1, 3, 6 or 12 months of the plan. Balance is added only after the gateway confirms
  the payment, and a paused plan starts again right away.
- History on that page lists top-ups, plan charges (hourly, grouped) and referral earnings added to the balance.
- Only people who can manage Users & Roles (normally the owner) can open Balance & Subscription.

## Refer & Earn (`/admin/subscription/refer-and-earn`)
- Set up SajiloBhoj for other restaurants as your clients: restaurant name, web address, brand colour
  (starts with your own), owner name, owner email and password. The client restaurant is live at once; its owner signs in with
  those details, picks a plan and gets the free trial.
- Every time a client tops up, you earn **10%** of the amount. Bonuses collect as **Available Earnings**
  on the Refer & Earn page (not in the plan balance). From there you choose:
  - **Add to plan balance** — any amount, instantly; it pays for your plan and restarts a paused plan.
  - **Withdraw** — to eSewa, Khalti (10-digit mobile number) or a bank account, at least Nrs 100 per request.
    The amount is set aside at once and the SajiloBhoj team sends it, usually within 2 working days.
    If it can't be sent, it comes back to your earnings.
- The Earnings History lists bonuses, amounts added to balance, and withdrawals with their status
  (Being paid, Paid with a reference, Returned).
- The page lists your client restaurants with their status (No plan yet, Free trial, Active, Paused)
  and the bonus each one has earned you.
- The client's web address must point to SajiloBhoj (DNS) before it opens.

## Common questions
- "How do I add a menu item?" → Menu → add item, choose a category, set name and price, save.
- "How do I add staff?" → Users & Roles → Staff → add, choose a role. To limit what they see, edit the role.
- "Why can't I see a page?" → Your role doesn't include it, or the restaurant's plan doesn't (Upgrade tag).
- "How do I take an order?" → Kitchen Display → add order, or the guest orders from the website / table QR.
- "How do I change the website colour?" → Settings → Theme.
- "My website says coming soon" → The plan is paused: top up on Balance & Subscription.
- "How do I print a table QR code?" → Menu → QR Code tab → choose the table → Print.
- "Who do I contact for help beyond this?" → The SajiloBhoj support team.
