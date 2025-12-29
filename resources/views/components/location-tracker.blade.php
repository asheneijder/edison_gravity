<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!navigator.geolocation) {
            console.log("Geolocation is not supported by this browser.");
            return;
        }

        // Always ask for permission (this triggers the browser prompt)
        navigator.geolocation.getCurrentPosition(function (position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Check if user is authenticated (web or admin)
            const isAuthenticated = {{ (auth('web')->check() || auth('admin')->check()) ? 'true' : 'false' }};

            if (isAuthenticated) {
                if (sessionStorage.getItem('location_sent')) {
                    return;
                }

                fetch('{{ route('user.location.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Network response was not ok');
                })
                .then(data => {
                    console.log('Location sent:', data);
                    sessionStorage.setItem('location_sent', 'true');
                })
                .catch(error => console.error('Error sending location:', error));
            } else {
                console.log("User not authenticated, location permission granted but skipped sending.");
            }
        }, function (error) {
            console.error("Geolocation error:", error);
        });
    });
</script>