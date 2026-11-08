@extends('frontend.theme_one.component.layout')

@section('content')
    @include('frontend.theme_one.component.page-banner', ['title' => 'Contact & Reservation', 'subtitle' => 'Get in touch with us or send a booking request.'])

    <section class="section-space">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    @php
                        $address = trim($settings['contact_address'] ?? '');
                        $phone   = trim($settings['contact_phone'] ?? '');
                        $email   = trim($settings['contact_email'] ?? '');
                        $hours   = trim($settings['opening_hours'] ?? '');
                        $socials = $socialLinks;
                        $hasAny  = $address || $phone || $email || $hours || $socials->isNotEmpty();
                    @endphp
                    <div class="contact-card h-100">
                        <h3 class="fw-bold mb-4"><i class="bi bi-telephone-outbound me-2"></i>Contact Details</h3>

                        @if ($address)
                        <div class="contact-info-item ci-row">
                            <span class="ci-icon"><i class="bi bi-geo-alt"></i></span>
                            <div>
                                <div class="small text-muted">Address</div>
                                <div class="fw-semibold" style="white-space:pre-line;">{{ $address }}</div>
                                <a class="ci-link" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address) }}" target="_blank" rel="noopener"><i class="bi bi-map me-1"></i>View on map</a>
                            </div>
                        </div>
                        @endif

                        @if ($phone)
                        <div class="contact-info-item ci-row">
                            <span class="ci-icon"><i class="bi bi-telephone"></i></span>
                            <div>
                                <div class="small text-muted">Phone</div>
                                <a class="fw-semibold ci-value" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a>
                            </div>
                        </div>
                        @endif

                        @if ($email)
                        <div class="contact-info-item ci-row">
                            <span class="ci-icon"><i class="bi bi-envelope"></i></span>
                            <div>
                                <div class="small text-muted">Email</div>
                                <a class="fw-semibold ci-value" href="mailto:{{ $email }}">{{ $email }}</a>
                            </div>
                        </div>
                        @endif

                        @if ($hours)
                        <div class="contact-info-item ci-row">
                            <span class="ci-icon"><i class="bi bi-clock"></i></span>
                            <div>
                                <div class="small text-muted">Opening Hours</div>
                                <div class="fw-semibold" style="white-space:pre-line;">{{ $hours }}</div>
                            </div>
                        </div>
                        @endif

                        @if ($socials->isNotEmpty())
                        <div class="contact-info-item ci-row">
                            <span class="ci-icon"><i class="bi bi-share"></i></span>
                            <div>
                                <div class="small text-muted mb-1">Follow Us</div>
                                <div class="ci-social">
                                    @foreach ($socials as $link)
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" aria-label="{{ $link['label'] }}" title="{{ $link['label'] }}"><i class="bi {{ $link['icon'] }}"></i></a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @unless ($hasAny)
                        <p class="section-subtitle mb-0">Contact details will appear here soon. Meanwhile, send us a message or a booking request.</p>
                        @endunless
                    </div>
                </div>
                <div class="col-lg-7" id="booking">
                    <div class="contact-card">
                        <h3 class="fw-bold mb-4"><i class="bi bi-calendar-check me-2"></i>Book a Table</h3>
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
                <div class="col-lg-12">
                    <div class="contact-card">
                        <h3 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2"></i>Quick Message</h3>
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
