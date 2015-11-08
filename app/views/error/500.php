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
    text-align: left;
    margin: 0 10% 0 10%;
    background-color: #0a0a0a;
    color: #004400;
    padding: 10px;
    border-radius: 5px;
  }
</style>
<div id="page500">
  <h1><?php echo $data['title']; ?></h1>
  <div id="error">
    <?php echo $data['error']; ?>
  </div>
  <?php echo \HMC\Language::tr('500_after_error'); ?>
  <br/>&nbsp;<br/>
</div>
