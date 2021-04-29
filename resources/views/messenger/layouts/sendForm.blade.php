<div class="messenger-sendCard">
    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <label class="my-2 mx-3"><span class="fas fa-paperclip"></span><input disabled='disabled' type="file" class="upload-attachment" name="file" accept="image/*, .txt, .rar, .zip"/></label>
        <textarea readonly='readonly' name="message" class="m-send app-scroll" placeholder="{{__('Type a message..')}}"></textarea>
        <button disabled='disabled' class="mx-3"><span class="fas fa-paper-plane"></span></button>
    </form>
</div>
