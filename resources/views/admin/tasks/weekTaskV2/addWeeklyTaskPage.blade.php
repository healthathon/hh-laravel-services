<div class="basic-form">
    <div class="form-group">
        <form class="form">
            {{ csrf_field() }}
            <div class="form-group">
                <label class="text-label"><span class="text-danger">*</span>Week No</label>
                <input type="number" id="week" class="form-control" placeholder="1" required="" min="0">
            </div>
            @for($i = 1; $i <= 7 ;$i++)
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="text-label"><span class="text-danger">*</span>Day {{$i}} Title</label>
                        <input type="text" id="day{{$i}}_title" class="form-control" placeholder="Task Name"
                               required="">
                    </div>
                    <div class="form-group  col-sm-4">
                        <label class="text-label">Day {{$i}} Message</label>
                        <input type="text" id="day{{$i}}_message" class="form-control" placeholder="Congratulations">
                    </div>
                    <div class="form-group  col-sm-4">
                        <label class="text-label">Day {{$i}} Badge</label>
                        <input type="text" id="day{{$i}}_badge" class="form-control" placeholder="Image url">
                    </div>
                </div>
            @endfor
            <div class="form-group">
                <label class="text-label">Week Badge</label>
                <input type="text" id="badge" class="form-control" placeholder="Weekly Badge Url">
            </div>
            <div class="form-group">
                <label class="text-label">Week Advise</label>
                <textarea id="advise" class="form-control" placeholder="Write an advise"></textarea>
            </div>
            <div class="form-group">
                <button type="button" class="form-control btn btn-success" id="saveData">Save</button>
            </div>
        </form>
    </div>
</div>
