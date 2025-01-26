
@section('title', __('text.print'))


<div class="body-inner print-tag">
    
<table width="150" align="center" border="1" cellspacing="0" cellpadding="10" >


<tr>
	<td align="center">
        <p><strong>{{$data->name}}</strong></p> 
        <p>{{$data->category}}</p>      
        <img src="{{$dataurl}}" width="100" />
        <p class="mt-2"><small>{{$data->code}}</small></p>                                    
	</td>
</tr>

</table>

</div>


