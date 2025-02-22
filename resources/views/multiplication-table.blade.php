<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Multiplication Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center">
    <div class="container mt-5">
        <h2 class="mb-4">Multiplication Table (1 to 10)</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>×</th>
                        <script>
                            for (let i = 1; i <= 10; i++) {
                                document.write(<th>${i}</th>);
                            }
                        </script>
                    </tr>
                </thead>
                <tbody>
                    <script>
                        for (let i = 1; i <= 10; i++) {
                            document.write("<tr>");
                            document.write(<th class="table-dark">${i}</th>);
                            for (let j = 1; j <= 10; j++) {
                                document.write(<td>${i * j}</td>);
                            }
                            document.write("</tr>");
                        }
                    </script>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>