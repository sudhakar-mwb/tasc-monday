const base_url = "http://localhost:8001/";
let data = {
	board: "",
	onboarding_columns: [],
	candidate_coulmns: [],
	extra_details: {
		key: "",
		time_stamp: "",
	},
};

let status_group = {};
/**
 * Manage Column Code Start
 */
function showLoader() {
	$("#full-loader").show();
}
function hideLoader() {
	$("#full-loader").hide();
}
$(document).ready(function () {
	const step2 = $("#form-step-2");

	$("#columns_details_submit").hide();
	$("#icon_inputs-wrapper").hide();

	async function fetchSavedData(url, id) {
		try {
			return fetch(base_url + `monday/admin/${url}/` + id);
		} catch (error) {
			console.log("Api error");
			return false;
		}
	}

	$("#input-brand").change(async function (e) {
		let val = e.target.value;
		data.board = val;
		if (val == "") return;
		showLoader();
		resetFields();

		const res = await fetchSavedData("get-board-columns", val);
		const columns = await res.json();
		const res_saved = await fetchSavedData("get-board-columns-data", val);
		const saved_data = await res_saved.json();
		if (saved_data)
			data = saved_data?.[0]?.columns ?? {
				board: val,
				onboarding_columns: [],
				candidate_coulmns: [],
				extra_details: {
					key: "",
					time_stamp: "",
				},
			};
		let options = columns
			.map((option) => `<option value="${option.id}">${option.title}</option>`)
			.join(" ");
		$("#candidate_columns").html(options);
		$("#onboarding_columns").html(options);
		$("#onboarding-updates-option").html(
			'<option value="">-- Select Column --</option>' + options
		);
		setValuesFromData();
		step2.show();
		console.log({ data: JSON.stringify(data) });
		$("#columns_details_submit").show();
		hideLoader();
	});

	function manageSelection(selected) {
		return selected.map((el, i) => {
			let obj = {
				id: el.id,
				name: el.text,
				icon: "",
				custom_title: "",
			};
			const index = data.candidate_coulmns.findIndex((_el) => _el.id == el.id);
			if (index != -1) obj = data.candidate_coulmns[index];
			return obj;
		});
	}

	$(document).on("input", "#icon_inputs>li>input.column_icon", function (e) {
		const index = $(this).attr("index");
		data.candidate_coulmns[index].icon = this.value;
	});
	$(document).on("input", "#icon_inputs>li>input.column_title", function (e) {
		const index = $(this).attr("index");
		data.candidate_coulmns[index].custom_title = this.value;
	});
	function populateColumnDetails() {
		$("#icon_inputs").html("");
		const inputs = data.candidate_coulmns
			.map(
				(li, i) => `<li class="mb-2">
    <label for="${"icon_input" + li.id}" class="form-label">For ${
					li.name
				}</label>
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

	$("#candidate_columns").change(function (e) {
		const selected_columns = $("#candidate_columns").select2("data");
		$("#icon_inputs-wrapper").show();
		data.candidate_coulmns = manageSelection(selected_columns);
		populateColumnDetails();
	});

	function resetFields() {
		$("#candidate_columns").val(null).trigger("change");
		$("#onboarding_columns").val(null).trigger("change");
		$("#onboarding-updates-option").val("");
		$("#icon_inputs-wrapper").hide();
	}
	function setValuesFromData() {
		populateColumnDetails();
		$("#candidate_columns").val(data.candidate_coulmns.map((el) => el.id));
		$("#candidate_columns").trigger("change");
		$("#onboarding_columns").val(data.onboarding_columns.map((el) => el.id));
		$("#onboarding_columns").trigger("change");
		$("#onboarding-updates-option").val(data.extra_details.key);
	}

	$("#onboarding_columns").change(function (e) {
		const selected_columns = $("#onboarding_columns").select2("data");
		data.onboarding_columns = selected_columns.map((el) => {
			return {
				id: el.id,
				name: el.text,
				icon: "",
				custom_title: "",
			};
		});
	});
	$("#onboarding-updates-option").change(function (e) {
		const selected_columns = this.value;
		data.extra_details.key = selected_columns;
	});

	$(document).ready(function () {
		$("#column_view_form").submit(async function (e) {
			e.preventDefault();
			console.log(data);
			showLoader();
			const response = await fetch(
				// "https://dummyjson.com/products/search?q=" + val
				base_url + "monday/admin/board-visiblilty",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
					},
					body: JSON.stringify(data),
				}
			);

			alert("columns details updated successfully.");
			hideLoader();
			const res = await response.json();
			console.log(res);
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
		let val = this.value.split(" ").join(",").split(",");
		status_group = val;
		this.value = val.join(", ");
	});
});