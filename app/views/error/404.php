<style>
	body {
		background-color: #ccc;
	}
	#page404 {
		background-color: #fff;
		text-align: center;
		width: 50%;
		min-width: 320px;
		margin-left: auto;
		margin-right: auto;
		border-radius: 10px;
		border: 1px solid #000;
	}
	#svg2 {
		min-width: 99%;
	}
</style>

<div id="page404">
	<h1>That page could not be found.</h1>

	<svg
	   xmlns:dc="http://purl.org/dc/elements/1.1/"
	   xmlns:cc="http://creativecommons.org/ns#"
	   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	   xmlns:svg="http://www.w3.org/2000/svg"
	   xmlns="http://www.w3.org/2000/svg"
	   version="1.1"
	   id="svg2"
	   viewBox="0 0 400 400.00001"
	   height="400"
	   width="400">
	  <defs
		 id="defs4" />
	  <metadata
		 id="metadata7">
		<rdf:RDF>
		  <cc:Work
			 rdf:about="">
			<dc:format>image/svg+xml</dc:format>
			<dc:type
			   rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
			<dc:title></dc:title>
			<cc:license
			   rdf:resource="http://creativecommons.org/publicdomain/zero/1.0/" />
		  </cc:Work>
		  <cc:License
			 rdf:about="http://creativecommons.org/publicdomain/zero/1.0/">
			<cc:permits
			   rdf:resource="http://creativecommons.org/ns#Reproduction" />
			<cc:permits
			   rdf:resource="http://creativecommons.org/ns#Distribution" />
			<cc:permits
			   rdf:resource="http://creativecommons.org/ns#DerivativeWorks" />
		  </cc:License>
		</rdf:RDF>
	  </metadata>
	  <g
		 transform="translate(0,-652.36216)"
		 id="layer1">
		<circle
		   r="197.48482"
		   cy="851.84692"
		   cx="201.52544"
		   id="path3336"
		   style="fill:#3227b1;stroke:#000000;stroke-opacity:1;fill-opacity:1" />
		<g
		   transform="translate(10.101525,18.182746)"
		   id="g4191">
		  <g
			 id="g4152">
			<rect
			   y="753.35699"
			   x="40.406101"
			   height="85.862999"
			   width="24.243666"
			   id="rect4138"
			   style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-opacity:1" />
			<rect
			   y="821.03723"
			   x="64.649765"
			   height="18.182745"
			   width="56.568542"
			   id="rect4140"
			   style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-opacity:1" />
			<rect
			   y="750.50891"
			   x="117.36007"
			   height="168.33072"
			   width="21.858606"
			   id="rect4142"
			   style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-width:1.3647505;stroke-opacity:1" />
		  </g>
		  <g
			 transform="translate(202.1217,-0.50506202)"
			 id="g4152-8">
			<rect
			   y="753.35699"
			   x="40.406101"
			   height="85.862999"
			   width="24.243666"
			   id="rect4138-0"
			   style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-opacity:1" />
			<rect
			   y="821.03723"
			   x="64.649765"
			   height="18.182745"
			   width="56.568542"
			   id="rect4140-6"
			   style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-opacity:1" />
			<rect
			   y="750.50891"
			   x="117.36007"
			   height="168.33072"
			   width="21.858606"
			   id="rect4142-1"
			   style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-width:1.3647505;stroke-opacity:1" />
		  </g>
		  <rect
			 style="fill:#d7a03c;fill-opacity:1;stroke:none;stroke-opacity:1"
			 id="rect4181"
			 width="76.771591"
			 height="168.69548"
			 x="152.53304"
			 y="752.34686" />
		  <rect
			 style="fill:#3227b1;fill-opacity:1;stroke:none;stroke-opacity:1"
			 id="rect4183"
			 width="45.456867"
			 height="132.32999"
			 x="167.68532"
			 y="770.5296" />
		</g>
	  </g>
	</svg>

	<ul>
		<li>Please check the address and try again.</li>
	
	<?php if(Core\Config::SITE_EMAIL() !== ''): ?>
		<li>If you believe you have reached this page in error you can <a href="mailto:<?php echo Core\Config::SITE_EMAIL(); ?>">email the site administrator.</a></li>
	<?php endif; ?>
	
		<li>You can also <a href="<?php echo Core\Config::SITE_URL(); ?>">return to the site's homepage.</a></li>
	
	</ul>
</div>
