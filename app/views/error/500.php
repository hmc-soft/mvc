<style>
	body {
		background-color: #ccc;
		font-family: "Helvetica","Tahoma",sans-serif;
	}
	#page500 {
		background-color: #fff;
		text-align: center;
		width: 50%;
		min-width: 320px;
		margin-left: auto;
		margin-right: auto;
		border-radius: 10px;
		border: 1px solid #000;
	}
  pre {
    font-family: "Andale Mono","Consolas",monospace;
  }
</style>
<div id="page500">
  <h1><?php echo $data['title']; ?></h1>
  <pre>
    <?php echo $data['error']; ?>
  </pre>
</div>
