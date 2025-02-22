
@section('title', __('text.print'))


<div class="body-inner print-tag">

<table width="150" align="center" border="1" cellspacing="0" cellpadding="10" >


<tr>
	<td align="center">
        <p><strong>{{$data->name}}</strong></p>
        <div class="d-flex" style="display: flex; gap:4px;    align-items: flex-end">
            <img src="{{$dataurl}}" width="100" />
            <p class="mt-2" style="transform: rotate(270deg); transform-origin: left top; width:60px; ">{{$data->size}}</p>
            {{-- <p>{{$data->category}}</p> --}}
            {{-- <p class="mt-2"><small>{{$data->code}}</small></p> --}}

        </div>
	</td>
</tr>

</table>

</div>


