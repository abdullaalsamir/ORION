<!DOCTYPE html>
<html>

<head>
</head>

<body style="font-family: Segoe UI, Inter, ui-sans-serif, system-ui, sans-serif; color:#333; line-height:1.6;">

    <div style="max-width:600px; margin:0 auto; border:1px solid #e1e1e1; border-radius:10px; overflow:hidden;">

        <div style="background-color:#0054a6; color:#ffffff; padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0;">
                    Product Complaint Report
                </h2>

                <div style="text-align:right; margin-left:auto;">
                    <span style="display:block; font-size:12px; opacity:0.8;">
                        Raising Date:
                    </span>
                    <span style="display:block; font-size:13px; font-weight:bold; opacity:0.8;">
                        {{ $complaint->complaint_date->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div style="padding:20px;">

            <div
                style="color:#0054a6; font-weight:bold; text-transform:uppercase; font-size:14px; border-bottom:2px solid #f0f0f0; padding-bottom:5px; margin-top:25px; margin-bottom:10px;">
                SECTION A: Product Identification
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Product Name:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->product_name }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Batch Number:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->batch_number }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Manufacturing Date:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->mfg_date->format('d/m/Y') }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Expiry Date:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->exp_date->format('d/m/Y') }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Strength:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->strength }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Dosage Form:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->dosage_form }}</span>
            </div>

            <div
                style="color:#0054a6; font-weight:bold; text-transform:uppercase; font-size:14px; border-bottom:2px solid #f0f0f0; padding-bottom:5px; margin-top:25px; margin-bottom:10px;">
                SECTION B: Nature of Complaint
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Type:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->complaint_type }}</span>
            </div>

            <div style="margin-top:5px;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Description:</span><br>
                <span
                    style="display:block; color:#333; font-size:14px; text-align:justify; margin-top:3px">{{ $complaint->complaint_description }}</span>
            </div>

            <div
                style="color:#0054a6; font-weight:bold; text-transform:uppercase; font-size:14px; border-bottom:2px solid #f0f0f0; padding-bottom:5px; margin-top:25px; margin-bottom:10px;">
                SECTION C: Complainant Information
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Name:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->complainant_name }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Contact:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->contact_number }}</span>
            </div>

            <div style="margin-bottom:10px; display:flex;">
                <span style="font-weight:bold; width:180px; color:#666; font-size:13px;">Address:</span>
                <span style="color:#333; font-size:14px; flex:1;">{{ $complaint->address }}</span>
            </div>

        </div>

        <div style="background-color:#f9f9f9; padding:15px; text-align:center; font-size:11px; color:#999;">
            This is an automated report generated from the ORION website.
        </div>

    </div>

</body>

</html>