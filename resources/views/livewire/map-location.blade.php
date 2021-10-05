<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Map
                </div>
                <div class="card-body">
                    <div wire:ignore id='map' style='width: 100%; height: 75vh;'></div>
                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Form
                </div>
                <div class="card-body">
                    <form action="" wire:submit.prevent="saveLocation">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Longitude</label>
                                    <input wire:model="long" type="text" class="form-control">
                                    @error('long')
                                 <small class="text-danger">
                                     {{ $message }}
                                 </small>
                             @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Latitude</label>
                                    <input wire:model="lat" type="text" name="" id="" class="form-control">
                                    @error('lat')
                                 <small class="text-danger">
                                     {{ $message }}
                                 </small>
                             @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <label for="">Title</label>
                             <input wire:model="title" type="text" class="form-control">
                             @error('title')
                                 <small class="text-danger">
                                     {{ $message }}
                                 </small>
                             @enderror
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea wire:model="description" type="text" class="form-control"></textarea>
                            @error('description')
                                 <small class="text-danger">
                                     {{ $message }}
                                 </small>
                             @enderror
                       </div>
                       <div class="form-group">
                        <label for="">Picture</label>
                           <div class="custom-file">
                            <input wire:model="image" type="file" class="custom-file-input" id="customFile">
                            <label class="custom-file-label dark-input" for="customFile">Choose file</label>
                           </div>
                           @error('image')
                           <small class="text-danger">
                               {{ $message }}
                           </small>
                       @enderror
                        @if($image)
                            <img src="{{ $image->temporaryUrl()}}" class="img-fluid">
                        @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark text-white btn-block" >Submit Location</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', () => {

        const defaultLocation = [106.67224158205, -6.363069862093909]

        mapboxgl.accessToken = '{{ env("MAPBOX_KEY") }}';
        var map = new mapboxgl.Map({
        container: 'map',
        center: defaultLocation,
        zoom: 11.15,
        style: 'mapbox://styles/mapbox/streets-v11'
        });




        const loadLocations = (geoJson) => {
            geoJson.features.forEach((location) => {
                const {geometry, properties} = location
                const {iconSize, locationId, title, image, description} = properties

                let markerElement = document.createElement('div')
                markerElement.className = 'marker' + locationId
                markerElement.id = locationId
                markerElement.style.backgroundImage = 'url({{asset("assets/marker.png")}})'
                markerElement.style.backgroundSize = 'cover'
                markerElement.style.width = '50px'
                markerElement.style.height = '50px'

                const imageStorage = '{{ asset("/storage/images") }}' + '/' + image

                const content = `
                <div class="overflow-y, auto;max-height:400px,width:100%">
                <table class="table table-sm mt-2">
                    <tbody>
                        <tr>
                            <td>Title</td>
                            <td>${title}</td>
                        </tr>
                        <tr>
                            <td>Picture</td>
                            <td><img src="${imageStorage}" loading="lazy" class="img-fluid" alt=""></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>${description}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
                `

                const popUp = new mapboxgl.Popup({
                    offset:25
                }).setHTML(content).setMaxWidth("400px")

                new mapboxgl.Marker(markerElement)
                .setLngLat(geometry.coordinates)
                .setPopup(popUp)
                .addTo(map)
            })
        }

        loadLocations({!! $geoJson !!})

        window.addEventListener('locationAdded', (e) => {
            loadLocations(JSON.parse(e.detail));
        })

        const style = "dark-v10"
        map.setStyle(`mapbox://styles/mapbox/${style}`)

        map.addControl(new mapboxgl.NavigationControl())

        map.on('click', (e) => {
        const longtitude = e.lngLat.lng
        const latitude = e.lngLat.lat

            @this.long = longtitude
            @this.lat = latitude
        });
    })
</script>

@endpush
