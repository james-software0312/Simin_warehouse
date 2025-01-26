@section('title', __('text.print'))

<style>
    .body-inner {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .print-tag {
        width: 30mm;
		height: 50mm;
		display: block;
    }

	table tr td {
		display: flex;
		height: 45mm;
		flex-direction: column;
		align-items: center;
	}

	p {
		margin: 2px 0;
	}

    /* Print-specific styles */
    @media print {
        @page {
            size: 30mm 51mm; /* Change to 40mm 60mm for a different size */
            margin: 0; /* Remove default margins */
        }

        body {
            margin: 0;
        }

        .print-tag {
            border: none; /* Optional: Remove border for printing */
            box-shadow: none; /* Remove shadow in print */
            width: 100%; /* Ensure it fits the page width */
            height: 100%; /* Ensure it fits the page height */
        }

        .row-spacing {
            margin: 0;
            border: none;
            width: 100%;
            display: block;
        }
    }
</style>

<div class="body-inner ">
    <table align="center" border="0" cellspacing="0" cellpadding="10">
        @foreach($ret_data as $index => $data)
        <tr class="row-spacing print-tag">
            <td align="center">
                <p>{{$data['data']->size}} {{$data['data']->itemsubtype}}</p>
                <img src="{{$data['dataurl']}}" width="100" />
                <p class="mt-2"><small>{{$data['data']->name}}</small></p>
            </td>
        </tr>
        @endforeach
    </table>
</div>
