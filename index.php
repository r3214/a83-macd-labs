<?php
require_once 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=emuhrezstorage;AccountKey=A/IFJZrWOhBBSHnY/lnTQBxv5iiddw8Q4KErx8CP51+ZUj10EHoZSuTGoT9Ttpykvno/R/TPYYztUW7AsdJdzg==;";
$containerName = "blobemuhrez";

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>
<html>
 <head>
 <Title>Analize Form</Title>
 <style type="text/css">
 	body { background-color: #fff; border-top: solid 10px #000;
 	    color: #333; font-size: .85em; margin: 20; padding: 20;
 	    font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
 	}
 	h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
 	h1 { font-size: 2em; }
 	h2 { font-size: 1.75em; }
 	h3 { font-size: 1.2em; }
 	table { margin-top: 0.75em; }
 	th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
 	td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
 </style>
 </head>
 <body>
 <h1>Analize Form</h1>
 <p>Upload your picture and click <strong>Upload</strong> to save it.</p>
 <form class="d-flex justify-content-center" action="index.php" method="post" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
				<input type="submit" name="submit" value="Upload">
			</form>
			
<h1>Total Files : <?php echo sizeof($result->getBlobs())?></h1>
<table class='table table-hover'>
	<thead>
		<tr>
			<th>File Name</th>
			<th>File URL</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
		do {
			foreach ($result->getBlobs() as $blob)
			{
				?>
				<tr>
					<td><?php echo $blob->getName() ?></td>
					<td><?php echo $blob->getUrl() ?></td>
					<td>
						<form action="analyze.php" method="post">
							<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
							<input type="submit" name="submit" value="Analyze!" class="btn btn-primary">
						</form>
					</td>
				</tr>
				<?php
			}
			$listBlobsOptions->setContinuationToken($result->getContinuationToken());
		} while($result->getContinuationToken());
		?>
	</tbody>
 </table>	
 </body>
</html>
