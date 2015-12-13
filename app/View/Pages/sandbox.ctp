<section>
	
	<h2>iFrame test</h2>
	
	<iframe src="http://localhost:8000/polls" frameborder="0">
		
		iFrame Test
	</iframe>
	
	<h2>Accordion test</h2>
	
	
	<dl class="accordion" data-accordion>
		<dd class="accordion-navigation">
			<a href="#panel1">Accordion 1</a>
			<div id="panel1" class="content active">
				<p>panel 1 text</p>
				
				<ul class="tabs" data-tab>
				  <li class="tab-title active"><a href="#panel2-1">Tab 1</a></li>
				  <li class="tab-title"><a href="#panel2-2">Tab 2</a></li>
				  <li class="tab-title"><a href="#panel2-3">Tab 3</a></li>
				  <li class="tab-title"><a href="#panel2-4">Tab 4</a></li>
				</ul>
				<div class="tabs-content">
				  <div class="content active" id="panel2-1">
				    <p>First panel content goes here...</p>
				  </div>
				  <div class="content" id="panel2-2">
				    <p>Second panel content goes here...</p>
				  </div>
				  <div class="content" id="panel2-3">
				    <p>Third panel content goes here...</p>
				  </div>
				  <div class="content" id="panel2-4">
				    <p>Fourth panel content goes here...</p>
				  </div>
				</div>
				
			</div>		
		</dd>
		<dd class="accordion-navigation">
			<a href="#panel2">Accordion 2</a>
			<div id="panel2" class="content">
				<p>Panel 2 text</p>
			</div>
		</dd>
		<dd class="accordion-navigation">
			<a href="#panel3">Accordion 3</a>
			<div id="panel3" class="content">
				<p>Panel 3 text</p>
			</div>
		</dd>
		
	</dl>
</section>

