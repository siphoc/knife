{*
	variables that are available:
	- {$widgetwidgetname}: contains all the data for this widget
*}

<div class="box" id="widgetwidgetname">
	<div class="heading">
		<h3></h3>
	</div>

	{option:widgetwidgetname}
	<div class="dataGridHolder">
		<table cellspacing="0" class="dataGrid">
			<tbody>
				{iteration:widgetwidgetname}
				<tr class="{cycle:'odd':'even'}">
				</tr>
				{/iteration:widgetwidgetname}
			</tbody>
		</table>
	</div>
	{/option:widgetwidgetname}

	<div class="footer">
		<div class="buttonHolderRight">

		</div>
	</div>
</div>