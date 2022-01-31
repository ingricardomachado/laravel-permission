@foreach($photos->chunk(4) as $items)
    <div class="row" style="margin-top:3mm">
    @foreach($items as $photo)
        <div class="col-sm-3">
            <div class="img-thumbnail">
                <div class="image view view-first">
                    <img src="{{ url('product_photo_thumbnail/'.$photo->id) }}" alt="image" />
                    <div class="mask">
                        <p></p>
                        <div class="tools tools-bottom">
                            <a class="popup-link" href="{{ url('product_photo/'.$photo->id) }}" title="{{ $photo->title }}"><i class="fas fa-search-plus"></i></a>
                            @if(session()->get('role')=='ADM')
                                <a href="#" onclick="showModalEdit('{{ $photo->id }}', '{{ $photo->title }}', {{ $photo->main }})"><i class="fas fa-pencil-alt"></i></a>
                                <a href="#" onclick="remove_photo({{ $photo->id }})"><i class="fas fa-trash"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="caption text-center">
                    <p>
                        <small>
                            {!! ($photo->main)?'<i class="fas fa-star" title="Imagen principal"></i> ':'' !!}</b>{{ $photo->title }}
                        </small>
                    </p>
                </div>
            </div>
        </div>
    @endforeach
    </div>
@endforeach
<script type="text/javascript">
    
    $('.popup-link').magnificPopup({
      type: 'image'
      // other options
    });    

</script>