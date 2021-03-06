/**
 * @projectDescription	Event controller for functionality in the "Find By" sub-tab.
 * @inherits 	i2b2.ONT.ctrlr
 * @namespace	i2b2.ONT.ctrlr.FindBy
 * @author		Nick Benik, Griffin Weber MD PhD
 * @version 	1.3
 * ----------------------------------------------------------------------------------------
 * updated 9-15-08: RC4 launch [Nick Benik] 
 */
console.group('Load & Execute component file: ONT > ctrlr > FindBy');
console.time('execute time');


i2b2.ONT.ctrlr.FindBy = {
	queryNewName: false,
	treeNameYui: false,
	treeCodeYui: false,

// ================================================================================================== //
	clickSearchName: function() {
		var f = $('ontFormFindName');
		var search_info = {};
		search_info.SearchStr = f.ontFindNameMatch.value;
		search_info.Category = f.ontFindCategory.options[f.ontFindCategory.selectedIndex].value;
		search_info.Strategy = f.ontFindStrategy.options[f.ontFindStrategy.selectedIndex].value;
		i2b2.ONT.ctrlr.FindBy.doNameSearch(search_info);
	},

// ================================================================================================== //
	doNameSearch: function(inSearchData) {
		// inSearchData is expected to have the following attributes:
		//   SearchStr:  what is being searched for
		//   Category: what category is being searched.  Blank for all
		//   Strategy: what matching strategy should be used

		// VERIFY that the above information has been passed		
		var f = false;
		if (Object.isUndefined(inSearchData)) return false;
		if (Object.isUndefined(inSearchData.SearchStr)) {
			alert('Please enter a search term.');
			return false;
		}
		if (Object.isUndefined(inSearchData.Category)) { inSearchData.Category=''; }
		if (Object.isUndefined(inSearchData.Strategy)) { 
			console.error('Matching Strategy has not been set');
			return false;
		}
		var s = inSearchData.Strategy;
		switch(s) {
			case "contains":	break;
			case "exact":		break;
			case "left":		break;
			case "right":		break;
			default:
				s = 'contains';
		}
		inSearchData.Strategy = s;
		
		// scope our callback function
		var scopedCallback = new i2b2_scopedCallback();
		scopedCallback.scope = this;
		// define our callback function
		scopedCallback.callback = function(results)
		{
			var treeObj = i2b2.ONT.view['find'].yuiTreeName;
			var jsTreeObjPath = 'i2b2.ONT.view.find.yuiTreeName';
			var tmpNode;
			i2b2.ONT.view.find.queryRequest = results.msgRequest;			
			i2b2.ONT.view.find.queryResponse = results.msgResponse;
			//delete old search results
			var tvRoot = treeObj.getRoot();
			treeObj.removeChildren(tvRoot);
			// THIS function is used to process the AJAX results of the getChild call
			//		results data object contains the following attributes:
			//			refXML: xmlDomObject <--- for data processing
			//			msgRequest: xml (string)
			//			msgResponse: xml (string)
			//			error: boolean
			//			errorStatus: string [only with error=true]
			//			errorMsg: string [only with error=true]
				
			var c = results.refXML.getElementsByTagName('concept');
			var x = results.refXML.getElementsByTagName('status');
			if (x[0] && x[0].firstChild) {
			        if (x[0].firstChild.data == 'MAX_EXCEEDED') {
			                alert ("Search results exceed maximum. " +
			                		"Please increase maximum allowable children in find options.");
			        }
			}
			for(var i=0; i<1*c.length; i++) {
				var o = new Object;
				o.xmlOrig = c[i];
				o.name = i2b2.h.getXNodeVal(c[i],'name');
				o.hasChildren = i2b2.h.getXNodeVal(c[i],'visualattributes').substring(0,2);
				o.level = i2b2.h.getXNodeVal(c[i],'level');
				o.key = i2b2.h.getXNodeVal(c[i],'key');
				o.tooltip = i2b2.h.getXNodeVal(c[i],'tooltip');
				o.icd9 = '';
				o.table_name = i2b2.h.getXNodeVal(c[i],'tablename');
				o.column_name = i2b2.h.getXNodeVal(c[i],'columnname');
				o.operator = i2b2.h.getXNodeVal(c[i],'operator');
				o.dim_code = i2b2.h.getXNodeVal(c[i],'dimcode');
				// append the data node
				var sdxDataNode = i2b2.sdx.Master.EncapsulateData('CONCPT',o);
				var renderOptions = {
					title: o.tooltip + o.name,
					dragdrop: "i2b2.sdx.TypeControllers.CONCPT.AttachDrag2Data",
					showchildren: true,
					icon: {
						root: "sdx_ONT_CONCPT_root.gif",
						rootExp: "sdx_ONT_CONCPT_root-exp.gif",
						branch: "sdx_ONT_CONCPT_branch.gif",
						branchExp: "sdx_ONT_CONCPT_branch-exp.gif",
						leaf: "sdx_ONT_CONCPT_leaf.gif"
					}
				};
				var sdxRenderData = i2b2.sdx.Master.RenderHTML(treeObj.id, sdxDataNode, renderOptions);
				i2b2.sdx.Master.AppendTreeNode(treeObj, treeObj.root, sdxRenderData);
			}
			// redraw treeview	
			treeObj.draw();
		}
		// add options
		var searchOptions = {};
		searchOptions.ont_max_records = "max='"+i2b2.ONT.view['find'].params.max+"' ";
		searchOptions.ont_synonym_records = i2b2.ONT.view['find'].params.synonyms;
		searchOptions.ont_hidden_records = i2b2.ONT.view['find'].params.hiddens;
		searchOptions.ont_category = inSearchData.Category;
		searchOptions.ont_search_strategy = inSearchData.Strategy;
		searchOptions.ont_search_string = inSearchData.SearchStr;
		i2b2.ONT.ajax.GetNameInfo("ONT:FindBy", searchOptions, scopedCallback);
	},

// ================================================================================================== //
	clickSearchCode: function() {
		var f = $('ontFormFindCode');
		var search_info = {};
		search_info.SearchStr = f.ontFindCodeMatch.value;
		search_info.Coding = f.ontFindCoding.options[f.ontFindCoding.selectedIndex].value;
		i2b2.ONT.ctrlr.FindBy.doCodeSearch(search_info);
	},

// ================================================================================================== //
	doCodeSearch: function(inSearchData) {
		// VERIFY that the above information has been passed		
		var f = false;
		if (Object.isUndefined(inSearchData)) return false;
		if (Object.isUndefined(inSearchData.SearchStr)) {
			alert('Please enter a search term.');
			return false;
		}
		var strMatch = inSearchData.SearchStr;
		if (Object.isUndefined(inSearchData.Coding)) { 
			alert('Please select a Coding System to search');
			return false;
		} else {
			if (inSearchData.Coding=='') { 
				alert('Please select a Coding System to search');
				return false;
			}
		}
		var code_system = inSearchData.Coding;
		// scope our callback function
		var scopedCallback = new i2b2_scopedCallback();
		scopedCallback.scope = this;
		// define our callback function
		scopedCallback.callback = function(results)
		{
			// THIS function is used to process the AJAX results of the getChild call
			//		results data object contains the following attributes:
			//			refXML: xmlDomObject <--- for data processing
			//			msgRequest: xml (string)
			//			msgResponse: xml (string)
			//			error: boolean
			//			errorStatus: string [only with error=true]
			//			errorMsg: string [only with error=true]
			var treeObj = i2b2.ONT.view.find.yuiTreeCode;
			var jsTreeObjPath = 'i2b2.ONT.view.find.yuiTreeCode';
			var tmpNode;
			i2b2.ONT.view.find.queryRequest = results.msgRequest;			
			i2b2.ONT.view.find.queryResponse = results.msgResponse;
			//delete old search results
			var tvRoot = treeObj.getRoot();
			treeObj.removeChildren(tvRoot);
			var c = results.refXML.getElementsByTagName('concept');
			for(var i=0; i<1*c.length; i++) {
				var o = new Object;
				o.xmlOrig = c[i];
				o.name = i2b2.h.getXNodeVal(c[i],'name');
				o.hasChildren = i2b2.h.getXNodeVal(c[i],'visualattributes').substring(0,2);
				o.level = i2b2.h.getXNodeVal(c[i],'level');
				o.key = i2b2.h.getXNodeVal(c[i],'key');
				o.tooltip = i2b2.h.getXNodeVal(c[i],'tooltip');
				o.icd9 = '';
				o.table_name = i2b2.h.getXNodeVal(c[i],'tablename');
				o.column_name = i2b2.h.getXNodeVal(c[i],'columnname');
				o.operator = i2b2.h.getXNodeVal(c[i],'operator');
				o.dim_code = i2b2.h.getXNodeVal(c[i],'dimcode');
				// append the data node
				var sdxDataNode = i2b2.sdx.Master.EncapsulateData('CONCPT',o);
				var renderOptions = {
					title: o.name,
					dragdrop: "i2b2.sdx.TypeControllers.CONCPT.AttachDrag2Data",
					showchildren: false,
					icon: {
						root: "sdx_ONT_CONCPT_root.gif",
						rootExp: "sdx_ONT_CONCPT_root-exp.gif",
						branch: "sdx_ONT_CONCPT_branch.gif",
						branchExp: "sdx_ONT_CONCPT_branch-exp.gif",
						leaf: "sdx_ONT_CONCPT_leaf.gif"
					}
				};
				var sdxRenderData = i2b2.sdx.Master.RenderHTML(treeObj.id, sdxDataNode, renderOptions);
				i2b2.sdx.Master.AppendTreeNode(treeObj, treeObj.root, sdxRenderData);
			}
			// redraw treeview	
			treeObj.draw();
		}
		// add options
		var searchOptions = {};
		searchOptions.ont_max_records = "max='"+i2b2.ONT.view['find'].params.max+"' ";
		searchOptions.ont_synonym_records = i2b2.ONT.view['find'].params.synonyms;
		searchOptions.ont_hidden_records = i2b2.ONT.view['find'].params.hiddens;
		searchOptions.ont_search_strategy = "exact";
		searchOptions.ont_search_coding = inSearchData.Coding;
		searchOptions.ont_search_string = inSearchData.SearchStr;
		i2b2.ONT.ajax.GetCodeInfo("ONT:FindBy", searchOptions, scopedCallback);
	}
}

console.timeEnd('execute time');
console.groupEnd();
