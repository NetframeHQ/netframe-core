<!doctype html>
<html lang="{{ \Lang::getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ $document->name }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body{
            margin:0;
            position:absolute;
            top:0;
            right:0;
            bottom:0;
            left:0;
        }
    </style>
</head>

<body>
    <div class="main-scroller" id="iframeEditor"></div>
    <script src="{{ env('ONLYOFFICE_IP_ADDRESS')!=null
        ? "https://".env('ONLYOFFICE_IP_ADDRESS').url()->route('onlyofficeds',[],false)
        : url()->route('onlyofficeds')
    }}/web-apps/apps/api/documents/api.js"></script>
    <script type="text/javascript">
        var docEditor;

        var onChange = function(event){
            var title = document.title.replace(/\*$/g, "");
            document.title = title + (event.data ? "*" : "");
        };

        var onOutdatedVersion = function (event) {
            location.reload(true);
        };

        new DocsAPI.DocEditor("iframeEditor", {
            "document": {
                "fileType": "{{$extension}}",
                "title": "{{$document->name}}",
                "url": "{{ env('ONLYOFFICE_IP_ADDRESS')!=null
                    ? "https://".env('ONLYOFFICE_IP_ADDRESS').url()->route('office.download',['mediaId' => $document->id], false)."?s=".$user->slug
                    : url()->route('office.download',['mediaId'=>$document->id])."?s=".$user->slug
                }}",
            },
            "documentType": "{{$document->getDocumentType()}}",
            "editorConfig": {
                "lang": "{{$user->lang}}",
                "mode": "edit",
                "user": {
                    "id": "{{$user->id}}",
                    "name": "{{$user->getNameDisplay()}}"
                },
                "callbackUrl": "{{ env('ONLYOFFICE_IP_ADDRESS')!=null
                    ? "https://".env('ONLYOFFICE_IP_ADDRESS').url()->route('office.save',['mediaId' => $document->id],false)
                    : url()->route('office.save',['mediaId' => $document->id])
                }}"
            },
            "events": {
                "onDocumentStateChange": onChange,
                "onOutdatedVersion": onOutdatedVersion,
            }
        });

    </script>
</body>
</html>
