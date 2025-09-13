<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.14.0/swagger-ui.css" />
      <style>
          html {
              box-sizing: border-box;
              overflow: -moz-scrollbars-vertical;
              overflow-y: scroll;
          }

          *,
          *:before,
          *:after {
              box-sizing: inherit;
          }

          body {
              margin: 0;
              background: #fafafa;
          }

      </style>
    <link rel="icon" type="image/png" href="swagger/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="swagger/favicon-16x16.png" sizes="16x16" />
  </head>

  <body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.14.0/swagger-ui-bundle.js" charset="UTF-8"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.14.0/swagger-ui-standalone-preset.js" charset="UTF-8"> </script>
    <script>
        window.onload = function() {
            //<editor-fold desc="Changeable Configuration Block">

            // the following lines will be replaced by docker/configurator, when it runs in a docker-container
            window.ui = SwaggerUIBundle({
                url: "api_list",
                dom_id: '#swagger-ui',
                deepLinking: false,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout"
            });

            //</editor-fold>
        };
    </script>
  </body>
</html>
