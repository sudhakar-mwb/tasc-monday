$(document).ready(function () {
  const step2 = $("#form-step-2");
  const data = {
    board: "",
    onboarding_columns: [],
    candidate_coulmns: [],
    extra_details: {
      key: "",
      time_stamp: "",
    },
  };
  $("#columns_details_submit").hide();
  $("#icon_inputs-wrapper").hide();
  $("#input-brand").change(async function (e) {
    let val = e.target.value;
    $("#columns_details_submit").show();
    data.board = val;
    const response = await fetch(
      // "https://dummyjson.com/products/search?q=" + val
      "http://localhost:8000/monday/admin/get-board-columns/" + val
    );
    const columns = await response.json();
    let options = columns
      .map((option) => `<option value="${option.id}">${option.title}</option>`)
      .join(" ");
    $("#candidate_columns").html(options);
    $("#onboarding_columns").html(options);
    $("#onboarding-updates-option").html('<option value="">-- Select Column --</option>'+options)
    step2.show();
  });

  function manageSelection(selected) {
    return selected.map((el, i) => {
      let obj = {
        id: el.id,
        name: el.text,
        icon: "",
      };
      const index = data.candidate_coulmns.findIndex((_el) => _el.id == el.id);
      if (index != -1) obj = data.candidate_coulmns[index];
      return obj;
    });
  }

  $(document).on("input", "#icon_inputs>li>input", function (e) {
    const index = $(this).attr("index");
    data.candidate_coulmns[index].icon = this.value;
  });

  function addIconInputs() {
    $("#icon_inputs").html("");
    const inputs = data.candidate_coulmns
      .map(
        (li, i) => `<li class="mb-2">
    <label for="${"icon_input" + li.id}" class="form-label">For ${
          li.name
        }</label>
    <input  class="form-control" id="${"icon_input" + li.id}" 
    index="${i}" type="text" value="${
          li.icon
        }" name="icons[]" placeholder="Enter icon class"></li>`
      )
      .join(" ");
    $("#icon_inputs").html(inputs);
  }

  $("#candidate_columns").change(function (e) {
    const selected_columns = $("#candidate_columns").select2("data");
    $("#icon_inputs-wrapper").show();
    data.candidate_coulmns = manageSelection(selected_columns);
    addIconInputs();
  });
  $("#onboarding_columns").change(function (e) {
    const selected_columns = $("#onboarding_columns").select2("data");
    data.onboarding_columns = selected_columns.map((el) => {
      return {
        id: el.id,
        name: el.text,
        icon: "",
      };
    });
  });
$("#onboarding-updates-option").change(function (e) {
  const selected_columns = this.value;
data.extra_details.key=selected_columns;
console.log({selected_columns})
});
  $(document).ready(function () {
    $("#column_view_form").submit(async function (e) {
      e.preventDefault();
     console.log(data);
     const response = await fetch(
      // "https://dummyjson.com/products/search?q=" + val
      "http://localhost:8000/monday/admin/board-visiblilty",{
method:"POST",
headers:{
  'Content-Type':'application/json'
},
body:JSON.stringify(data)
      }
    );
    const res = await response.json();
console.log(res)
    });
  });

  function setData() {}
});
