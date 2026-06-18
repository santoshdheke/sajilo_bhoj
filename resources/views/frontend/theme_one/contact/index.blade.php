@extends('frontend.theme_one.component.layout')

@section('content')
    <section class="page-banner">
        <div class="container text-center">
            <h1>Contact & Reservation</h1>
            <p class="mb-0 text-white-50">Get in touch with us or send a booking request.</p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 fade-up">
                    <div class="contact-card h-100">
                        <h3 class="fw-bold mb-4">Contact Details</h3>
                        <div class="contact-info-item">
                            <div class="small text-muted">Address</div>
                            <div class="fw-semibold">{{ $settings['contact_address'] ?: 'Kathmandu, Nepal' }}</div>
                        </div>
                        <div class="contact-info-item">
                            <div class="small text-muted">Phone</div>
                            <div class="fw-semibold">{{ $settings['contact_phone'] ?: '+977-9800000000' }}</div>
                        </div>
                        <div class="contact-info-item">
                            <div class="small text-muted">Email</div>
                            <div class="fw-semibold">{{ $settings['contact_email'] ?: 'hello@sajilobhoj.com' }}</div>
                        </div>
                        <div class="pt-3">
                            <h5 class="fw-bold">Opening Hours</h5>
                            <p class="section-subtitle mb-0" style="white-space:pre-line;">{{ $settings['opening_hours'] ?: "Sunday - Friday: 10:00 AM - 10:00 PM\nSaturday: 11:00 AM - 11:00 PM" }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 fade-up" id="booking">
                    <div class="contact-card">
                        <h3 class="fw-bold mb-4">Book a Table</h3>
                        <form id="bookingForm" class="booking-form">
                            <div class="row g-3">
                                <div class="col-md-6"><input type="text" class="form-control" placeholder="Your Name"
                                        required></div>
                                <div class="col-md-6"><input type="tel" class="form-control" placeholder="Phone Number"
                                        required></div>
                                <div class="col-md-6"><input type="date" class="form-control" required></div>
                                <div class="col-md-6"><input type="time" class="form-control" required></div>
                                <div class="col-md-6">
                                    <select class="form-select" required>
                                        <option value="">Guests</option>
                                        <option>1 Person</option>
                                        <option>2 People</option>
                                        <option>4 People</option>
                                        <option>6+ People</option>
                                    </select>
                                </div>
                                <div class="col-md-6"><input type="email" class="form-control"
                                        placeholder="Email Address"></div>
                                <div class="col-12">
                                    <textarea class="form-control" rows="4" placeholder="Special Request"></textarea>
                                </div>
                                <div class="col-12"><button class="btn btn-sb-primary" type="submit">Send Booking
                                        Request</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-12 fade-up">
                    <div class="contact-card">
                        <h3 class="fw-bold mb-4">Quick Message</h3>
                        <form id="contactForm" class="contact-form">
                            <div class="row g-3">
                                <div class="col-md-6"><input type="text" class="form-control" placeholder="Full Name"
                                        required></div>
                                <div class="col-md-6"><input type="email" class="form-control" placeholder="Email Address"
                                        required></div>
                                <div class="col-12"><input type="text" class="form-control" placeholder="Subject"
                                        required></div>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" placeholder="Your message" required></textarea>
                                </div>
                                <div class="col-12"><button class="btn btn-sb-primary" type="submit">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
