<!DOCTYPE html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap layout</title>

    <!-- DHTMLX Gantt CSS and JS -->
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>

    <!-- Bootstrap 5 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Using jQuery for a consistent user experience, though not strictly required for the modal -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <style>
        .bx-search {
            color: white; /* Change this to your preferred color */
        }
        .bx-menu {
          color: white;
        }
        .text-truncate {
          color: #a5a5a5cc;
        }
      </style>
	<style>
		html, body {
			height: 100%;
			padding: 0px;
			margin: 0px;
		}

		.weekend {
			background: #f4f7f4 !important;
		}

		.gantt_selected .weekend {
			background: #dadcda !important;
		}

		.well {
			text-align: right;
		}

		@media (max-width: 991px) {
			.nav-stacked > li {
				float: left;
			}
		}

		.container-fluid .row {
			margin-bottom: 10px;
		}
        

		.container-fluid .gantt_wrapper {
			height: 650px;
			width: 100%;
		}

		.gantt_container {
			border-radius: 4px;
		}

		.gantt_grid_scale {
			background-color: transparent;
		}

		.gantt_hor_scroll {
			margin-bottom: 1px;
		}

        .container-fluid>.navbar-collapse, .container-fluid>.navbar-header, .container>.navbar-collapse, .container>.navbar-header {
            background-color: #004080;
        }
        .navbar-inverse {
            /* Mengganti warna latar belakang */
            background-color: #004080; /* Warna merah yang Anda inginkan */
            
            /* Mengganti warna border */
            border-color: #080808; /* Warna border yang Anda inginkan */
            
            /* Menonaktifkan background-image yang menggunakan gradien */
            background-image: none; /* Ini akan menonaktifkan semua gradien */
            
            /* Jika Anda ingin memastikan tidak ada filter IE lama yang aktif */
            filter: none;
        }
        
	</style>
    
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<nav class="navbar navbar-expand-lg navbar-inverse">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="#">
            <label class="text-white mb-0">{{ $project->name }}</label>
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarGantt"
            aria-controls="navbarGantt" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu items -->
        <div class="collapse navbar-collapse text-end" >
            

            <!-- Back button -->
            <a href="{{ route('v1.projects.detail',['project'=>$project->obfuscated_id]) }}"
               class="btn btn-sm" style="color: #f45e07ff;">
                ← Back
            </a>
        </div>
    </div>
</nav>

		</div>
	</div>
	<div class="row">
		
		<div class="col-md-10 col-md-pull-2">
			<div class="gantt_wrapper panel" id="gantt_here"></div>
		</div>
        <div class="col-md-2 col-md-push-10">
			<div class="card" >
				<div class="card-header">
					<label>Gantt info</label>
				</div>
				<div class="card-body">
					<ul class="list-group list-group-flush" id="gantt_info">
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

            
<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Modal Structure (already present in your code) -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="modalTaskName"></span></p>
                <p><strong>Assigned To:</strong> <span id="modalAssignedName"></span></p>
                <p><strong>Start:</strong> <span id="modalTaskStart"></span></p>
                <p><strong>End:</strong> <span id="modalTaskEnd"></span></p>
            </div>
        </div>
    </div>
</div>
<style>
    .gantt-bar-green .gantt_task_bar {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.gantt-bar-blue .gantt_task_bar {
    background-color: #007bff !important;
    border-color: #007bff !important;
}
.gantt-bar-red .gantt_task_bar {
    background-color: #f82c0cff !important;
    border-color: #f82c0cff!important;
}

</style>
<script>
	var demo_tasks = {
		data:[
			{id:1, text:"Office itinerancy", type:gantt.config.types.project, progress: 0.4, open: false},
			{id:2, text:"Office facing", type:gantt.config.types.project, start_date:"02-04-2023", duration:"8", progress: 0.6, parent:"1", open: true},
			{id:3, text:"Furniture installation", type:gantt.config.types.project, start_date:"11-04-2023", duration:"8", parent:"1", progress: 0.6, open: true},
			{id:4, text:"The employee relocation", type:gantt.config.types.project, start_date:"13-04-2023", duration:"6", parent:"1", progress: 0.5, open: true},
			{id:5, text:"Interior office", start_date:"02-04-2023", duration:"7", parent:"2", progress: 0.6, open: true},
			{id:6, text:"Air conditioners check", start_date:"03-04-2023", duration:"7", parent:"2", progress: 0.6, open: true},
			{id:7, text:"Workplaces preparation", start_date:"11-04-2023", duration:"8", parent:"3", progress: 0.6, open: true},
			{id:8, text:"Preparing workplaces", start_date:"14-04-2023", duration:"5", parent:"4", progress: 0.5, open: true},
			{id:9, text:"Workplaces importation", start_date:"14-04-2023", duration:"4", parent:"4", progress: 0.5, open: true},
			{id:10, text:"Workplaces exportation", start_date:"14-04-2023", duration:"3", parent:"4", progress: 0.5, open: true},
			{id:11, text:"Product launch", type:gantt.config.types.project, progress: 0.6, open: true},
			{id:12, text:"Perform Initial testing", start_date:"03-04-2023", duration:"5", parent:"11", progress: 1, open: true},
			{id:13, text:"Development", type:gantt.config.types.project, start_date:"02-04-2023", duration:"7", parent:"11", progress: 0.5, open: true},
			{id:14, text:"Analysis", start_date:"02-04-2023", duration:"6", parent:"11", progress: 0.8, open: true},
			{id:15, text:"Design", type:gantt.config.types.project, start_date:"02-04-2023", duration:"5", parent:"11", progress: 0.2, open: false},
			{id:16, text:"Documentation creation", start_date:"02-04-2023", duration:"7", parent:"11", progress: 0, open: true},
			{id:17, text:"Develop System", start_date:"03-04-2023", duration:"2", parent:"13", progress: 1, open: true},
			{id:25, text:"Beta Release", start_date:"06-04-2023",type:gantt.config.types.milestone, parent:"13", progress: 0, open: true},
			{id:18, text:"Integrate System", start_date:"08-04-2023", duration:"2", parent:"13", progress: 0.8, open: true},
			{id:19, text:"Test", start_date:"10-04-2023", duration:"4", parent:"13", progress: 0.2, open: true},
			{id:20, text:"Marketing", start_date:"10-04-2023", duration:"4", parent:"13", progress: 0, open: true},
			{id:21, text:"Design database", start_date:"03-04-2023", duration:"4", parent:"15", progress: 0.5, open: true},
			{id:22, text:"Software design", start_date:"03-04-2023", duration:"4", parent:"15", progress: 0.1, open: true},
			{id:23, text:"Interface setup", start_date:"03-04-2023", duration:"5", parent:"15", progress: 0, open: true},
			{id:24, text:"Release v1.0", start_date:"15-04-2023",type:gantt.config.types.milestone, parent:"11", progress: 0, open: true}
		],
		links: [
			{id: "1", source: "1", target: "2", type: "1"},

			{id: "2", source: "2", target: "3", type: "0"},
			{id: "3", source: "3", target: "4", type: "0"},
			{id: "4", source: "2", target: "5", type: "2"},
			{id: "5", source: "2", target: "6", type: "2"},
			{id: "6", source: "3", target: "7", type: "2"},
			{id: "7", source: "4", target: "8", type: "2"},
			{id: "8", source: "4", target: "9", type: "2"},
			{id: "9", source: "4", target: "10", type: "2"},

			{id: "10", source: "11", target: "12", type: "1"},
			{id: "11", source: "11", target: "13", type: "1"},
			{id: "12", source: "11", target: "14", type: "1"},
			{id: "13", source: "11", target: "15", type: "1"},
			{id: "14", source: "11", target: "16", type: "1"},

			{id: "15", source: "13", target: "17", type: "1"},
			{id: "16", source: "17", target: "25", type: "0"},
			{id: "23", source: "25", target: "18", type: "0"},
			{id: "17", source: "18", target: "19", type: "0"},
			{id: "18", source: "19", target: "20", type: "0"},
			{id: "19", source: "15", target: "21", type: "2"},
			{id: "20", source: "15", target: "22", type: "2"},
			{id: "21", source: "15", target: "23", type: "2"},
			{id: "22", source: "13", target: "24", type: "0"}
		]
	};

	var getListItemHTML = function (type, count, active) {
		return '<li class="list-group-item ' + (active ? ' activex"' : '"') + '>'+ type + 's <span class="badge bg-info">' + count + '</span></li>';
	};

	var updateInfo = function () {
		var state = gantt.getState(),
			tasks = gantt.getTaskByTime(state.min_date, state.max_date),
			types = gantt.config.types,
			result = {},
			html = "",
			active = false;

		// get available types
		result[types.task] = 0;
		result[types.project] = 0;
		result[types.milestone] = 0;

		// sort tasks by type
		for (var i = 0, l = tasks.length; i < l; i++) {
			if (tasks[i].type && result[tasks[i].type] != "undefined")
				result[tasks[i].type] += 1;
			else
				result[types.task] += 1;
		}
		// render list items for each type
		for (var j in result) {
			if (j == types.task)
				active = true;
			else
				active = false;
			html += getListItemHTML(j, result[j], active);
		}

		document.getElementById("gantt_info").innerHTML = html;
	};

	gantt.templates.scale_cell_class = function (date) {
		if (date.getDay() == 0 || date.getDay() == 6) {
			return "weekend";
		}
	};
	gantt.templates.timeline_cell_class = function (item, date) {
		if (date.getDay() == 0 || date.getDay() == 6) {
			return "weekend";
		}
	};

	gantt.templates.rightside_text = function (start, end, task) {
		if (task.type == gantt.config.types.milestone) {
			return task.text;
		}
		return "";
	};

	gantt.config.columns = [
		{name: "text", label: "Task name", resize: true, width: "*", tree: true},
		{name: "start_date", label: "Start time", resize: true, align: "center", width: 80},
		{name: "duration", label: "Duration", resize: true, align: "center", width: 60},
        {
            name: "assigned_to_user_id",
            label: "Assigned To",
            resize: true,
            align: "center",
            width: 120,
            template: function(task) {
                // If you pass user names instead of IDs to Gantt data
                // you can directly return task.assigned_to_user_name
                return task.assigned_to_user_name 
                ? task.assigned_to_user_name 
                : (task.assigned_to_user_id ? "User #" + task.assigned_to_user_id : "");
            }
        }
	];

	gantt.config.grid_width = 390;
	gantt.config.date_grid = "%F %d";
	gantt.config.scale_height = 60;
	gantt.config.scales = [
		{unit: "day", step: 1, format: "%d %M"},
		{unit: "week", step: 1, format: "Week #%W"}
	];
    gantt.templates.task_class = function (start, end, task) {
        return "custom-bar"; // shared class for all bars
    };

    gantt.templates.task_text = function (start, end, task) {
        const color = task.color || "#3498db"; // fallback color
        return `<div style="background-color:${color}; 
                            border-color:${color}; 
                            height:100%; width:100%; 
                            color:white; padding:2px;">
                    ${task.text}
                </div>`;
    };

	gantt.attachEvent("onAfterTaskAdd", function (id, item) {
		updateInfo();
	});
	gantt.attachEvent("onAfterTaskDelete", function (id, item) {
		updateInfo();
	});

    // --- UPDATED CODE to show the modal ---
    gantt.attachEvent("onTaskClick", function(id, e) {
        var task = gantt.getTask(id);

        // Populate modal fields with task data
        document.getElementById("modalTaskName").innerText = task.text;
        document.getElementById("modalAssignedName").innerText = task.assigned_to_user_name;
        
        // Format the dates for display
        var startDate = new Date(task.start_date);
        var endDate = new Date(task.end_date);
        document.getElementById("modalTaskStart").innerText = startDate.toLocaleDateString();
        document.getElementById("modalTaskEnd").innerText = endDate.toLocaleDateString();

        // Show the Bootstrap 5 modal
        var myModal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
        myModal.show();

        // Return false to prevent the default DHTMLX Gantt editor from opening
        return false;
    });

	gantt.init("gantt_here");
	//gantt.parse(demo_tasks);
    gantt.parse(@json($ganttData));
	updateInfo();
    document.getElementById("gantt_here").style.height = (window.innerHeight-120) + "px";

</script>


</body>