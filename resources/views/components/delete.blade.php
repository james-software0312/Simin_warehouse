<div class="modal fade" id="DeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <!-- Modal content -->
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('text.delete_data') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <p>{{ __('text.confirm_delete') }}</p>
                {{$slot}}
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
                    <span class="material-symbols-rounded">close</span> {{ __('text.close') }}
                </button>
                <button type="button" class="btn btn-primary d-flex align-items-center" id="modalDeleteButton">
                    <span class="material-symbols-rounded">check</span> {{ __('text.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>
