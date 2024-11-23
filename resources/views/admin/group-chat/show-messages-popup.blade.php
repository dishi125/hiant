<div class="modal-dialog" style="max-width: 70%;">
    <div class="modal-content">
        <div class="modal-header justify-content-center">
            {{--            <h5>Messages</h5>--}}
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body justify-content-center">
            <div class="row align-items-xl-center mb-3">
                <div class="w-100 tab-content" id="myTabContent2">
                    <div class="tab-pane fade show active" id="referral-data" role="tabpanel" aria-labelledby="referral-data">
                        <div class="table-responsive">
                            <table class="table table-striped" id="message-data-table">
                                <thead>
                                <tr>
                                    <th class="mr-3">Message</th>
                                    <th class="mr-3">User name</th>
                                    <th class="mr-3">Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group_chat_messages as $message)
                                    @if($message->type!="kick")
                                    <tr>
                                        @if($message->type=="images")
                                        <?php $images = explode(",",$message->message); ?>
                                        <td>
                                            @foreach($images as $image)
                                            <img src="{{ $image }}" onclick="showImage('{{ $image }}')" alt="Image" class="reported-client-images pointer m-1" width="50" height="50">
                                            @endforeach
                                        </td>

                                        @elseif($message->type=="videos")
                                        <?php $videos = \App\Models\GroupChatRoomMessageFile::where('message_id',$message->id)->where('group_id',$message->group_id)->get(); ?>
                                        <td>
                                            @foreach($videos as $video)
                                            <img src="{{ $video->video_thumbnail }}" onclick="showImage('{{ $video->file }}')" alt="Video" class="reported-client-images pointer m-1" width="50" height="50">
                                            @endforeach
                                        </td>

                                        @else
                                        <td>{{ $message->message }}</td>
                                        @endif

                                        <td>{{ $message->name }}</td>
                                        <td>{{ \App\Http\Controllers\Controller::formatDateTimeCountryWiseFromMs($message->created_at, $adminTimezone)}}</td>
                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $("#message-data-table").DataTable({order: [[ 2, "desc" ]]});
</script>
