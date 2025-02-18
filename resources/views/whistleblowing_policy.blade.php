@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
    <!-- custom-icon Breadcrumb-->
    <nav aria-label="breadcrumb">

        <ol class="breadcrumb breadcrumb-custom-icon">
            @foreach ($breadcrumb as $item )
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">{{ $item }}</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>Whistleblowning Policy</h2>
    <style>
        .form-container {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button {
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
    <style>
        h1 {
      font-size: 20px;
      margin-bottom: 15px;
    }

    .section-title {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .content {
      margin-left: 20px;
    }

    ul {
      list-style: disc;
      margin-left: 20px;
    }
    .policy-info {
      color: #333; /* Gray text color */
      font-size: 14px;
      margin-bottom: 10px;
      margin-top: 20px;
    }

    hr {
      border: 1px solid #ccc; /* Gray line */
      margin-top: 10px;
    }
  </style>
    <p>Vibtech Genesis is committed to maintaining the highest standards of integrity, transparency, and accountability. This Whistleblowing Policy provides a safe and confidential channel for employees, business partners, and stakeholders to report concerns about unethical, illegal, or fraudulent activities within the company.</p>

    <h1 class="section-title">1. Scope</h1>
    <p class="content">This policy applies to all employees, contractors, suppliers, and any individuals who have a business relationship with Vibtech Genesis. Reports may include, but are not limited to:</p>
    <ul>
      <li>Fraud, corruption, or financial misconduct</li>
      <li>Violations of company policies or legal regulations</li>
      <li>Unsafe workplace practices</li>
      <li>Harassment, discrimination, or abuse of authority</li>
    </ul>

    <h1 class="section-title">2. Reporting Channels</h1>
    <p class="content">Individuals can report concerns confidentially through the following channels:</p>
    <ul>
      <li>Email: [confidential email address]</li>
      <li>Whistleblowing Hotline: [hotline number]</li>
      <li>Anonymous Submission: [web form or dropbox]</li>
    </ul>

    <h1 class="section-title">3. Confidentiality & Protection</h1>
    <p class="content">All reports will be treated with strict confidentiality. Vibtech Genesis strictly prohibits retaliation against whistleblowers. Any form of intimidation or harassment against a whistleblower will result in disciplinary action.</p>
    <h1 class="section-title">4. Investigation & Follow-Up</h1>
  <p>All reports will be assessed and investigated by an independent review team. If misconduct is confirmed, appropriate corrective actions will be taken. Whistleblowers may be informed of the outcome where applicable.</p>

  <h1 class="section-title">5. Policy Review</h1>
  <p>This policy will be reviewed periodically to ensure effectiveness and compliance with legal requirements.</p>

  <div class="policy-info">
    Policy Last Created on 25/03/2015
  </div>

  <hr>
    <h1 class="section-title">Report an Incident</h1>
    <p>Please describe the incident you want to report below. Your identity will be kept strictly confidential.</p>

    <div class="form-container">
        <label for="staffName">Staff Name:</label>
        <input type="text" id="staffName" name="staffName" value="John Doe">

        <label for="staffID">Staff ID:</label>
        <input type="text" id="staffID" name="staffID" value="S095">

        <label for="description">Description of Incident:</label>
        <textarea id="description" name="description"></textarea>

        <button type="submit">Submit</button>
    </div>
</div>
@endsection
