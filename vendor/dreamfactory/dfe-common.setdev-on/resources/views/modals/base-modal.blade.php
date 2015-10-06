<?php
/**
 * Generic modal dialog for bootstrap 3.x
 *
 * Sections:
 *
 * modal-header     => the header of the modal
 * modal-body       => the body of the modal
 * modal-footer     => the footer of the modal
 *
 * Optional Variables:
 *
 * $modalId         => optional "id" for the modal div
 * $modalTitle      => optional title of the modal, if null, modal is title-less
 * $closeButton     => set to FALSE to not show a close button. Defaults to
 */
?>
<div class="modal fade" {{ isset($modalId) ? 'id="' . $modalId . '"' : null }}>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @section('modal-header')
                    @if( !isset($closeButton) || false !== $closeButton )
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    @endif
                    @if( isset($modalTitle) )
                        <h4 class="modal-title">{{ $modalTitle }}</h4>
                    @endif
                @show
            </div>
            <div class="modal-body">
                @section('modal-body')
                @show
            </div>
            <div class="modal-footer">
                @section('modal-footer')
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Ok</button>
                @show
            </div>
        </div>
    </div>
</div>
