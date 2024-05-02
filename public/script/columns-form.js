/**
 * to remove refrence of an object
 * @param  state an object that have to be deep copy
 * @returns
 */
function deepCopy(state) {
	return JSON.parse(JSON.stringify(state));
}

const initial_state = {
	board: "",
	onboarding_columns: [],
	candidate_coulmns: [],
	sub_headings_column: [],
	// documents_columns: [],
	card_section: {
		column1: "",
		column2: "",
	},
  required_columns:{
    profession:"",
    overall_status:""
  },
	extra_details: {
		key: "",
		time_stamp: "",
		chart_embed_code: "",
		form_embed_code: "",
	},
};
let status_group = {};

let data = deepCopy(initial_state);

/**
 * responsible to popoulate inputs for candiate columns details
 */
function populateColumnDetails() {
	$("#icon_inputs").html("");
	const inputs = data.candidate_coulmns
		.map(
			(li, i) => `<li class="mb-2">
  <label for="${"icon_input" + li.id}" class="form-label">For ${li.name}</label>
  <input  class="form-control mb-2 column_icon" id="${"icon_input" + li.id}" 
  index="${i}" type="text" value="${
				li.icon
			}" name="icons[]" placeholder="Enter column icon">
      <input  class="form-control column_title" id="${"icon_input" + li.id}" 
      index="${i}" type="text" value="${
				li.custom_title
			}" name="icons[]" placeholder="Enter column title">
      </li>`
		)
		.join(" ");
	$("#icon_inputs").html(inputs);
}

/**
 * responsible for show od badges in selected order of Select 2
 * @param {*} id
 * @param {*} forkey
 */
function updateSelectedInOrder(id, forkey) {
	var $selectedContainer = $(id).next().find(".select2-selection__rendered");
	$selectedContainer.empty();
	data[forkey].forEach(function (item) {
		var liElement = $(
			'<li class="select2-selection__choice" title="' +
				item.name +
				'" data-select2-id="' +
				item.id +
				'">'
		);
		var removeButton = $(
			'<button type="button" data_id="' +
				item.id +
				'" class="select2-selection__choice__remove" tabindex="-1" title="Remove item" aria-label="Remove item" aria-describedby="select2-' +
				forkey +
				"-container-choice-" +
				item.id +
				'"><span aria-hidden="true">×</span></button>'
		);
		var displayText = $(
			'<span class="select2-selection__choice__display" id="select2-' +
				forkey +
				"-container-choice-" +
				item.id +
				'">' +
				item.name +
				"</span>"
		);
		removeButton.on("click", function () {
			var data_id = $(this).attr("data_id");
			var index = data[forkey].findIndex(function (item) {
				return item.id === data_id;
			});
			if (index !== -1) {
				data[forkey].splice(index, 1); // Remove unselected value
				$(id).val(data[forkey].map((el) => el.id));
				updateSelectedInOrder(id, forkey);
			}
			populateColumnDetails();
		});
		liElement.append(removeButton);
		liElement.append(displayText);
		$selectedContainer.append(liElement);
	});
}

$(document).ready(function () {
	$(".js-example-basic-multiple").select2({
		placeholder: "Choose Columns",
	});

	/**
	 * Manage Column Code Start ----------------------------------------------------------------------------------
	 */
	const step2 = $("#form-step-2");
	$("#columns_details_submit").hide();
	$("#icon_inputs-wrapper").hide();

	/**
	 * on select onboarding columns
	 */
	$("#onboarding_columns")
		.on("select2:select", function (e) {
			const el = e.params.data;
			data.onboarding_columns.push({
				id: el.id,
				name: el.text,
				icon: "",
				custom_title: "",
			});
			updateSelectedInOrder("#onboarding_columns", "onboarding_columns");
		})
		.on("select2:unselect", function (e) {
			var index = data.onboarding_columns.findIndex(function (item) {
				return item.id === e.params.data.id;
			});
			if (index !== -1) {
				data.onboarding_columns.splice(index, 1); // Remove unselected value
				updateSelectedInOrder("#onboarding_columns", "onboarding_columns");
			}
		});

	/**
	 * on select candidate columns
	 */
	$("#candidate_columns")
		.on("select2:select", function (e) {
			const el = e.params.data;
			$("#icon_inputs-wrapper").show();
			data.candidate_coulmns.push({
				id: el.id,
				name: el.text,
				icon: "",
				custom_title: "",
			});
			populateColumnDetails();
			updateSelectedInOrder("#candidate_columns", "candidate_coulmns");
		})
		.on("select2:unselect", function (e) {
			var index = data.candidate_coulmns.findIndex(function (item) {
				return item.id === e.params.data.id;
			});
			if (index !== -1) {
				data.candidate_coulmns.splice(index, 1); // Remove unselected value
			}
			populateColumnDetails();
			updateSelectedInOrder("#candidate_columns", "candidate_coulmns");
		});

	/**
	 * on select subheadings
	 */
	$("#sub_headings")
		.on("select2:select", function (e) {
			const el = e.params.data;
			$("#icon_inputs-wrapper").show();
			data.sub_headings_column.push({
				id: el.id,
				name: el.text,
				icon: "",
				custom_title: "",
			});
			populateColumnDetails();
			updateSelectedInOrder("#sub_headings", "sub_headings_column");
		})
		.on("select2:unselect", function (e) {
			var index = data.candidate_coulmns.findIndex(function (item) {
				return item.id === e.params.data.id;
			});
			if (index !== -1) {
				data.candidate_coulmns.splice(index, 1); // Remove unselected value
			}
			populateColumnDetails();
			updateSelectedInOrder("#sub_headings", "sub_headings_column");
		});

	function showLoader() {
		$("#full-loader").show();
	}
	function hideLoader() {
		$("#full-loader").hide();
	}

	/**
	 *
	 * @param {*} url
	 * @param {*} id
	 * @returns promise
	 */
	async function fetchSavedData(url, id) {
		try {
			return fetch(base_url + `onboardify/admin/${url}/` + id);
		} catch (error) {
			console.log("Api error", error);
			return false;
		}
	}

	$("#input-board-select").change(async function (e) {
		let val = e.target.value;
		body = deepCopy(initial_state);
		data.board = val;
		if (val == "") return;
		showLoader();
		resetFields();

		const res = await fetchSavedData("get-board-columns", val);
		const columns = await res.json();
		const res_saved = await fetchSavedData("get-board-columns-data", val);
		const saved_data = await res_saved.json();
		if (saved_data?.[0]?.columns) data = { ...data, ...saved_data[0].columns };

		if (data?.["candidate_coulmns"]?.length > 0) {
			$("#icon_inputs-wrapper").show();
		}

		let options = columns
			.map((option) => `<option value="${option.id}">${option.title}</option>`)
			.join(" ");

		$("#candidate_columns").html(options);
		$("#onboarding_columns").html(options);
		$("#sub_headings").html(options);
		const single_select_options =
			'<option value="">-- Select Column --</option>' + options;
		$("#onboarding-updates-option").html(single_select_options);
		$("#card-column-1").html(single_select_options);
		$("#card-column-2").html(single_select_options);
    $("#profession_column").html(single_select_options);
		$("#overall_status").html(single_select_options);
		setValuesFromData();
		step2.show();

		$("#columns_details_submit").show();

		hideLoader();
	});

	$(document).on("input", "#icon_inputs>li>input.column_icon", function (e) {
		const index = $(this).attr("index");
		data.candidate_coulmns[index].icon = this.value;
	});
	$(document).on("input", "#chart_embed_code", function (e) {
		data.extra_details.chart_embed_code = this.value;
	});
	$(document).on("input", "#form_embed_code", function (e) {
		data.extra_details.form_embed_code = this.value;
	});
	$(document).on("input", "#icon_inputs>li>input.column_title", function (e) {
		const index = $(this).attr("index");
		data.candidate_coulmns[index].custom_title = this.value;
	});

	function resetFields() {
		$("#candidate_columns").val(null).trigger("change");
		$("#onboarding_columns").val(null).trigger("change");
		$("#sub_headings").val(null).trigger("change");
		$("#onboarding-updates-option").val("");
		$("#card-column-1").val("");
		$("#card-column-2").val("");
    $("#profession_column").val("");
		$("#overall_status").val("");
		$("#icon_inputs-wrapper").hide();
		$("#chart_embed_code").val("");
		$("#form_embed_code").val("");
	}
	function setValuesFromData() {
		populateColumnDetails();
		setTimeout(() => {
			updateSelectedInOrder("#onboarding_columns", "onboarding_columns");
			updateSelectedInOrder("#candidate_columns", "candidate_coulmns");
			// updateSelectedInOrder("#documents_columns", "documents_columns");
			updateSelectedInOrder("#sub_headings", "sub_headings_column");
		}, 100);
		$("#candidate_columns").val(data.candidate_coulmns.map((el) => el.id));
		$("#candidate_columns").trigger("change");
		$("#onboarding_columns").val(data.onboarding_columns.map((el) => el.id));
		$("#onboarding_columns").trigger("change");
		$("#sub_headings").val(data.sub_headings_column.map((el) => el.id));
		$("#sub_headings").trigger("change");
		// $("#documents_columns").val(data?.documents_columns.map((el) => el.id));
		// $("#documents_columns").trigger("change");
		$("#onboarding-updates-option").val(data.extra_details.key);
		$("#chart_embed_code").val(data.extra_details?.chart_embed_code ?? "");
		$("#form_embed_code").val(data.extra_details?.form_embed_code ?? "");

		$("#card-column-1").val(data.card_section.column1);
		$("#card-column-2").val(data.card_section.column2);
    $("#profession_column").val(data.required_columns.profession);
		$("#overall_status").val(data.required_columns.overall_status);
    

	}

	$("#onboarding-updates-option").change(function (e) {
		const selected_columns = this.value;
		data.extra_details.key = selected_columns;
	});
	$("#card-column-1").change(function (e) {
		data.card_section.column1 = this.value;
	});
	$("#card-column-2").change(function (e) {
		data.card_section.column2 = this.value;
	});
	$("#profession_column").change(function (e) {
		data.required_columns.profession = this.value;
	});
	$("#overall_status").change(function (e) {
		data.required_columns.overall_status = this.value;
	});

	$(document).ready(function () {
		$("#column_view_form").submit(async function (e) {
			e.preventDefault();

			showLoader();
			try {
				let response = await fetch(
					// "https://dummyjson.com/products/search?q=" + val
					base_url + "onboardify/admin/board-visiblilty",
					{
						method: "POST",
						headers: {
							"Content-Type": "application/json",
						},
						body: JSON.stringify(data),
					}
				);

				if (!response.ok) throw new Error("HTTP status " + response.status);
				else {
					response = await response.json();
					if (response?.status) {
						alert(response?.message);
						hideLoader();
					} else {
						throw new Error("HTTP status " + response.status);
					}
				}
			} catch (error) {
				console.log("Api error", error);
				alert("Something wents wrong.");
			}
			hideLoader();
		});
	});
	/**
	 * Manage Column Code End
	 */

	// ===================================================================================================

	/**
	 * Manage Status Background
	 */

	$("ul.color-section .color-input").blur(function (e) {
		let val = this.value.split(",").filter((el) => el);
		this.value = val.join(", ");
		const color = $(this).attr("current_color");
		console.log(first);
		status_group[color] = val;
	});

	$(document).ready(function () {
		$("#status_view_form").submit(async function (e) {
			e.preventDefault();
			var formData = {};
			$(this)
				.find("input, textarea")
				.each(function () {
					formData[$(this).attr("name")] = $(this)
						.val()
						.split(",")
						.map((el) => el.trim())
						.filter((el) => el !== "");
				});

			showLoader();
			try {
				let response = await fetch(
					base_url + "onboardify/admin/colour-mapping",
					{
						method: "POST",
						headers: {
							"Content-Type": "application/json",
						},
						body: JSON.stringify(formData),
					}
				);

				if (!response.ok) throw new Error("HTTP status " + response.status);
				else {
          response= await response.json()
					if (response?.status) {
						alert(response?.message);
						hideLoader();
					} else {
						throw new Error("HTTP status " + response.status);
					}
				}
			} catch (error) {
				console.log("Api error", error);
				alert("Something wents wrong.");
			}

			hideLoader();
		});
	});
});
