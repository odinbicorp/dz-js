<!DOCTYPE html>
<html lang="en">
<head>
  <title>DEMO</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background: gray; color:#f1f7fa; font-weight:bold;">
                         DEMO
                    </div>
                    <div class="card-body">                    
                        <form action="{{ route('store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                            <div class="form-group">
                                <label for="document">Documents</label>
                                <div class="needsclick dropzone" id="document-dropzone">
                            </div>
                            <button type="submit" id="submitBtn" class="btn btn-primary mt-5">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script>
  var uploadedDocumentMap = {}
  const documentDropzoneBtn = $('#document-dropzone');
  Dropzone.options.documentDropzone = {
    url: "{{ route('uploads') }}",
    maxFilesize: 256, // MB
    addRemoveLinks: true,
    chunking: true,
    forceChunking: true,
    chunkSize: 1000000,
    parallelUploads: 1,
    maxFiles: 1,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
      uploadedDocumentMap[file.name] = response.name;
      console.log(response.name);
      showSubmitButton();
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDocumentMap[file.name]
      }
      $('form').find('input[name="document[]"][value="' + name + '"]').remove()
    },
    canceled: function(file) {
        // Kiểm tra nếu file đã được upload hoàn tất
         // Gửi request về server để xóa các file chunks
         $.ajax({
            headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            url: "{{ route('cancelUpload') }}",
            method: 'POST',
            data: { dzuuid: file.upload.uuid },
            success: function(response) {
                console.log('Chunks deleted:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error deleting chunks:', error);
            }
        });
    },
    init: function () {
        this.on("addedfile", function () {
            hideSubmitButton();
        });
        this.on("canceled", function (file) {
           showSubmitButton();
        });
      @if(isset($project) && $project->document)
        var files =
          {!! json_encode($project->document) !!}
        for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
        }
      @endif
    }
  }

//   documentDropzoneBtn.on('click',function(e){
//     alert(123);
//   });

  function hideSubmitButton() {
    $('#submitBtn').hide();
  }

  function showSubmitButton() {
    $('#submitBtn').show();
  }
</script>
</html>
