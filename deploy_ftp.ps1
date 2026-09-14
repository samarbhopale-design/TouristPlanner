$ftpServer = "ftp://ftpupload.net/htdocs"
$username  = "if0_42916037"
$password  = "Qeuk7S1ay0Fd"
$localDir  = "d:\samar"

$filesToUpload = @(
    "style.css",
    "db.php",
    "header.php",
    "footer.php",
    "index.php",
    "login.php",
    "register.php",
    "logout.php",
    "add_trip.php",
    "view_trips.php",
    "estimator.php",
    "database.sql",
    "setup_db.php"
)

Write-Host "Connecting to InfinityFree FTP ($ftpServer)..." -ForegroundColor Cyan

foreach ($file in $filesToUpload) {
    $localPath = Join-Path $localDir $file
    if (-not (Test-Path $localPath)) {
        Write-Warning "File not found: $localPath"
        continue
    }

    $ftpUrl = "$ftpServer/$file"
    Write-Host "Uploading $file -> $ftpUrl ..." -NoNewline

    try {
        $ftp = [System.Net.FtpWebRequest]::Create($ftpUrl)
        $ftp.Credentials = New-Object System.Net.NetworkCredential($username, $password)
        $ftp.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftp.UseBinary = $true
        $ftp.KeepAlive = $false

        $content = [System.IO.File]::ReadAllBytes($localPath)
        $ftp.ContentLength = $content.Length

        $requestStream = $ftp.GetRequestStream()
        $requestStream.Write($content, 0, $content.Length)
        $requestStream.Close()

        $response = $ftp.GetResponse()
        Write-Host " [OK] ($($content.Length) bytes)" -ForegroundColor Green
        $response.Close()
    }
    catch {
        Write-Host " [FAILED]: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`nAll files uploaded successfully!" -ForegroundColor Green
