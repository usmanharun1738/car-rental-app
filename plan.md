# Project Proposal: Professional Car Rental Management System (MVP)

## 🎯 Objective
Build a fully functional, production-ready Car Rental System to learn and demonstrate mastery of **Laravel 11, Livewire, TailwindCSS, Alpine.js, and FilamentPHP**. The system will feature a high-performance client-facing frontend and a robust, professional admin panel.

---

## 🛠 Tech Stack

- **Backend Framework**: Laravel 11.x (Latest Stable)
- **Admin Panel**: FilamentPHP (TALL Stack based)
- **Client Frontend**: Livewire, TailwindCSS, Alpine.js
- **Database**: MySQL
- **File Storage**: Native Local Filesystem (Simulating S3 interface)

---

## 🏗 System Architecture

### 1. Architectural Pattern
We will move away from the traditional "Fat Controller" or "Repository" patterns in favor of a **Domain-Oriented Action Pattern**.

- **MVC**: Standard Model-View-Controller for routing and basic HTTP handling.
- **Actions (`app/Actions`)**: Single-responsibility classes for all business logic (e.g., `CreateBookingAction`, `CheckVehicleAvailabilityAction`). This ensures code is reusable, testable, and composable.
- **Enums & State Machines**: Strict typing for statuses (Vehicle Status, Booking Status) using PHP 8.1+ Enums.
- **No Repository Layer**: We will use Eloquent directly or via Actions.

### 2. Directory Structure (Feature-Based)
```text
app/
├── Actions/              # Business Logic (The "Brain")
│   ├── Bookings/
│   │   ├── CreateBookingAction.php
│   │   ├── CancelBookingAction.php
│   │   └── CheckAvailabilityAction.php
│   ├── Vehicles/
│   └── Payments/
├── Enums/                # Strict Types
│   ├── VehicleStatus.php
│   ├── BookingStatus.php
│   └── PaymentStatus.php
├── Models/               # Eloquent Models
├── Livewire/             # Client-Facing UI Components
│   └── Front/
│       ├── BookingWizard.php
│       └── VehicleListing.php
├── Filament/             # Admin Panel Resources (Auto-generated)
└── Services/             # External Integrations (e.g., PaystackService)
```

---

## � Modules & Features Breakdown

### 1️⃣ Vehicles Module
**Goal**: Complete fleet management with strict status control.

- **Features**:
    - **CRUD**: Create, Edit, Delete vehicles via Filament.
    - **Status Management**: State machine flow (Available ↔ Booked ↔ Maintenance).
    - **Media Management**: Upload multiple images and documents (Registration, Insurance) using native filesystem.
    - **Maintenance Logs**: Track when and why a vehicle is unavailable (e.g., "Oil Change", "Accident Repair").
- **Data Structure**:
    - `vehicles` table: `id`, `make`, `model`, `plate_number`, `daily_rate`, `status` (Enum).
    - `maintenance_logs` table: `vehicle_id`, `description`, `start_date`, `end_date`, `cost`.

### 2️⃣ Bookings Module (The Core)
**Goal**: Robust booking engine with conflict prevention.

- **Features**:
    - **Real-time Availability**: Check vehicle availability for specific dates.
    - **Concurrency Locking**: Use `DB::transaction()` and `lockForUpdate()` to prevent double-booking during high traffic.
    - **Buffer Time**: Configurable "turnaround time" (e.g., 60 mins) between bookings for cleaning/prep.
    - **Dynamic Pricing**: Calculate total based on days * rate (extensible for seasonal pricing).
    - **Driver Assignment**: Option to book with a driver.
    - **Terms & Conditions**: Digital acceptance of rules.
- **Data Structure**:
    - `bookings` table: `user_id`, `vehicle_id`, `start_time`, `end_time`, `total_price`, `status`, `notes`.

### 3️⃣ Payments Module
**Goal**: Flexible payment tracking.

- **Features**:
    - **Polymorphic Payments**: The `payments` table will use `payable_type` and `payable_id`. This allows payments to be attached to Bookings, Fines, or future entities without schema changes.
    - **Receipts**: Upload proof of payment (screenshots/PDFs).
    - **Invoices**: Generate simple PDF invoices.
    - **Gateways**: Manual (Cash/Transfer) initially, with structure for Paystack integration.
- **Data Structure**:
    - `payments` table: `payable_id`, `payable_type`, `amount`, `method`, `status`, `transaction_reference`.

### 4️⃣ Customer & Admin Modules
- **Customers**:
    - Profile management.
    - ID Verification (Upload Driver's License).
    - Booking History.
- **Admin (Filament)**:
    - Dashboard with widgets (Income, Active Bookings, Fleet Status).
    - Role-Based Access Control (Admin vs. Staff).

---

## 🚀 Implementation Roadmap

### Phase 1: Foundation & Configuration
- [ ] **Frontend Auth**: Install Laravel Breeze (Livewire/Volt) for customer authentication.
- [ ] **Storage**: Configure symbolic links for image uploads.
- [ ] **Enums**: Define `VehicleStatus`, `BookingStatus`, `PaymentStatus` to ensure data integrity.
- [ ] **Database**: Create Migrations and Models for `Vehicle`, `Booking`, `Payment`, and `MaintenanceLog`.

### Phase 2: Admin Panel (Filament)
- [ ] **Vehicle Management**: Create `VehicleResource` with image upload and status management.
- [ ] **Customer Management**: Create `CustomerResource` to view users and verify IDs.
- [ ] **Booking Management**: Create `BookingResource` for admins to view calendar and manage bookings.

### Phase 3: Business Logic (The "Brain")
- [ ] **Availability Action**: Implement `CheckVehicleAvailabilityAction` to handle date overlaps.
- [ ] **Booking Action**: Implement `CreateBookingAction` with **Concurrency Locking** (`lockForUpdate`).
- [ ] **Pricing Action**: Implement dynamic price calculation logic.

### Phase 4: Client Frontend (Livewire)
- [ ] **Home & Search**: Build a responsive vehicle listing page with filters.
- [ ] **Booking Wizard**: Create a multi-step booking form (Date -> Vehicle -> Extras -> Payment).
- [ ] **User Dashboard**: Allow customers to view their booking history and status.

### Phase 5: Payments & Polish
- [ ] **Payments**: Implement manual receipt upload and invoice generation.
- [ ] **Refinement**: Add buffer times between bookings and polish the UI.