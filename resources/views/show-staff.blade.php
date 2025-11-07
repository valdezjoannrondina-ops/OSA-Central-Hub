<div class="page-section">
    <div class="container">
      <h1 class="text-center mb-5 wow fadeInUp">Meet the OSA Staff</h1>

  <!-- The ID 'doctorSlideshow' is fine as long as your JS initializes the Owl Carousel targeting this ID. -->
  <div class="owl-carousel wow fadeInUp" id="staffSlideshow">
        
        @foreach($staff as $staff)
          <div class="item">
            <div class="card-staff">
              <div class="header">
                <img height="300px" src="{{ \Illuminate\Support\Facades\Storage::url($staff->image) }}" alt="{{ $staff->first_name }} {{ $staff->last_name }}">
                <div class="meta">
                  <a href="#"><span class="mai-call"></span></a>
                  <a href="#"><span class="mai-logo-whatsapp"></span></a>
                </div>
              </div>
              <div class="body">
                <p class="text-xl mb-0">{{ $staff->first_name }} {{ $staff->middle_name }} {{ $staff->last_name }}</p>
                <span class="text-sm text-grey">{{ $staff->designation }}</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <!-- Carousel navigation buttons -->
      <div class="text-center mt-4">
        <button id="prevStaff" class="btn btn-secondary btn-lg mx-2">Previous Staff</button>
        <button id="nextStaff" class="btn btn-primary btn-lg mx-2">Next Staff</button>
      </div>
    </div>
  </div>