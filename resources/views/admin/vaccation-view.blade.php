<style>
.modal-dialog {
    width:700px !important;
    margin:40px auto;
}
.modal-content {
    border-radius:12px;
    overflow:hidden;
    border:none;
}
.modal-header {
    background:#000;
    color:#fff;
    padding:15px 20px;
}
.modal-header h4 { 
    margin:0;
    font-weight:600;
}
.modal-header .close {
    color:#fff;
    opacity:1;
}
.v-box {
    border:1px solid #e9ecef;
    border-radius:10px;
    padding:15px;
    margin-bottom:10px;
    background:#fff;
}
.v-title {
    font-size:14px;
    color:#777;
    margin-bottom:5px;
}
.v-value {
    font-size: 12px;
    font-weight: 600;
    color: #222;
}
.comment-box {
    background:#f8f9fa;
    border-left:4px solid #0d6efd;
    padding:15px;
    border-radius:8px;
    min-height:50px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .3px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}
.status-badge i{
    font-size:14px;
}
.pending{
    background:#fff8e1;
    color:#ff9800;
    border:1px solid #ffd54f;
}
.approved{
    background:#e8f5e9;
    color:#28a745;
    border:1px solid #81c784;
}
.decline{
    background:#ffebee;
    color:#dc3545;
    border:1px solid #ef9a9a;
}
.user-icon{
    width:70px;
    height:70px;
    border-radius:50%;
    background:#0d6efd;
    color:#fff;
    line-height:70px;
    text-align:center;
    font-size:24px;
    margin:auto;
}
.emp-name{
    text-align:center;
    font-size:20px;
    font-weight:bold;
    margin-top:10px;
    margin-bottom:25px;
}
</style>
<div class="container-fluid">
  <div class="user-icon">
 User Name
</div>
    <div class="emp-name">
        {{ \App\Models\User::find($data->user_id)->name ?? 'N/A' }}
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="v-box">
                <div class="v-title">
                    Vacation From
                </div>
                <div class="v-value">
                    {{ date('d M, Y',strtotime(str_replace('_','-',$data->vacc_start))) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="v-box">
                <div class="v-title">
                    Vacation To
                </div>
                <div class="v-value">
                    {{ date('d M, Y',strtotime(str_replace('_','-',$data->vacc_end))) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="v-box">
                <div class="v-title">
                    Created On
                </div>
                <div class="v-value">
                    {{ date('d M, Y',strtotime($data->created_at)) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="v-box">
                <div class="v-value">
                    @if($data->vacc_status==0)
                    <span class="status-badge pending">
                        <i class="fa fa-clock-o"></i>Pending
                    </span>
                    @elseif($data->vacc_status==1)
                    <span class="status-badge approved">
                        <i class="fa fa-check-circle"></i>Approved
                    </span>
                    @else
                    <span class="status-badge decline">
                        <i class="fa fa-times-circle"></i>Declined
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="v-box">
                <div class="v-title">
                   Comments
                </div>
                <div class="comment-box">
                    {{ $data->vacc_comments ?: 'No comments available.' }}
                </div>
            </div>
        </div>
    </div>
</div>