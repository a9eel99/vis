@php
    $isRtl = $lang === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $isScored = $inspection->template->isScored();
    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeMap = ['excellent'=>['#059669','#ecfdf5'],'good'=>['#2563eb','#eff6ff'],'needs_attention'=>['#d97706','#fffbeb'],'critical'=>['#dc2626','#fef2f2']];
    $gradeColors = $gradeMap[$gradeStr] ?? ['#6b7280','#f9fafb'];
    $allPhotos = collect();
    foreach ($sectionResults as $data) {
        foreach ($data['results'] as $item) {
            if ($item['result'] && $item['result']->media) {
                foreach ($item['result']->media as $m) {
                    if ($m->isImage()) {
                        $allPhotos->push(['url' => $m->url, 'name' => $m->original_name, 'question' => $item['question']->label, 'section' => $data['section']->name]);
                    }
                }
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isRtl ? 'تقرير الفحص' : 'Inspection Report' }} - {{ $inspection->reference_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--b9:#1e3a8a;--b7:#1d4ed8;--b6:#2563eb;--b1:#dbeafe;--b0:#eff6ff;--g6:#059669;--g0:#ecfdf5;--a6:#d97706;--a0:#fffbeb;--r6:#dc2626;--r0:#fef2f2;--g50:#f8fafc;--g100:#f1f5f9;--g200:#e2e8f0;--g300:#cbd5e1;--g400:#94a3b8;--g500:#64748b;--g700:#334155;--g800:#1e293b;--g900:#0f172a;--rad:12px;--sh:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);--shL:0 4px 12px rgba(0,0,0,.08)}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Cairo','IBM Plex Sans',sans-serif;background:var(--g100);color:var(--g800);direction:{{$dir}};line-height:1.6;min-height:100vh;-webkit-font-smoothing:antialiased}
        .topbar{background:var(--b9);padding:14px 0}.topbar-in{max-width:680px;margin:0 auto;padding:0 16px;display:flex;align-items:center;justify-content:space-between}.brand{display:flex;align-items:center;gap:10px;color:#fff}.brand img{height:36px;border-radius:6px}.brand-n{font-weight:700;font-size:1rem}.ref-tag{background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.3px}
        .ctn{max-width:680px;margin:0 auto;padding:0 16px 32px}
        .hero{background:#fff;border-radius:0 0 var(--rad) var(--rad);padding:24px;text-align:center;box-shadow:var(--shL);margin-bottom:16px;position:relative;overflow:hidden}.hero::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--b6),var(--b9))}.vname{font-size:1.3rem;font-weight:800;color:var(--g900);margin-bottom:2px}.vsub{font-size:.82rem;color:var(--g500);margin-bottom:16px}.dateline{font-size:.75rem;color:var(--g400);margin-top:12px}
        .gr{width:110px;height:110px;margin:0 auto 8px;position:relative}.gr svg{width:100%;height:100%;transform:rotate(-90deg)}.gr .bg{fill:none;stroke:var(--g200);stroke-width:8}.gr .fg{fill:none;stroke:{{$gradeColors[0]}};stroke-width:8;stroke-linecap:round;stroke-dasharray:314;stroke-dashoffset:{{314-(314*($inspection->percentage??0)/100)}};transition:stroke-dashoffset 1.5s ease}.gr .in{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}.gr .pct{font-size:1.6rem;font-weight:800;color:{{$gradeColors[0]}};line-height:1}.gr .ps{font-size:.8rem;font-weight:600}.gl{display:inline-block;padding:4px 16px;border-radius:20px;background:{{$gradeColors[1]}};color:{{$gradeColors[0]}};font-weight:700;font-size:.85rem}
        .dbadge{display:inline-block;padding:8px 20px;border-radius:20px;background:var(--b0);color:var(--b7);font-weight:700;font-size:.9rem;margin-top:8px}
        .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:16px}.st{background:#fff;border-radius:var(--rad);padding:14px 10px;text-align:center;box-shadow:var(--sh)}.st-l{font-size:.68rem;color:var(--g400);font-weight:600;text-transform:uppercase;letter-spacing:.5px}.st-v{font-size:1.1rem;font-weight:800;color:var(--g800);margin-top:2px}
        .alert-c{background:var(--r0);border:2px solid #fca5a5;border-radius:var(--rad);padding:12px 16px;color:#991b1b;font-weight:700;font-size:.85rem;text-align:center;margin-bottom:16px}
        .card{background:#fff;border-radius:var(--rad);box-shadow:var(--sh);margin-bottom:12px;overflow:hidden}.ch{padding:14px 18px;font-weight:700;font-size:.88rem;color:var(--g700);border-bottom:1px solid var(--g100);display:flex;align-items:center;gap:8px}.cb{padding:14px 18px}
        .ir{display:flex;padding:7px 0;border-bottom:1px solid var(--g50)}.ir:last-child{border-bottom:none}.il{width:110px;flex-shrink:0;font-size:.76rem;color:var(--g400);font-weight:600}.iv{flex:1;font-size:.88rem;font-weight:500}
        .sh{background:var(--b9);color:#fff;padding:12px 18px;font-weight:700;font-size:.88rem;cursor:pointer;display:flex;justify-content:space-between;align-items:center;user-select:none;-webkit-tap-highlight-color:transparent}.sh:active{background:#1e3578}.sa{transition:transform .3s;font-size:.8rem}.sb{display:block}
        .qr{padding:12px 18px;border-bottom:1px solid var(--g100)}.qr:last-child{border-bottom:none}.qt{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}.ql{font-weight:600;font-size:.85rem;color:var(--g800);flex:1}.cd{display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--r6);margin-{{$isRtl?'left':'right'}}:4px;vertical-align:middle}.qa{margin-top:3px;font-size:.82rem;color:var(--g500)}.qp{display:inline-block;padding:2px 10px;border-radius:10px;font-size:.72rem;font-weight:700}.ph{background:#d1fae5;color:#065f46}.pm{background:#fef3c7;color:#92400e}.pl{background:#fee2e2;color:#991b1b}.qrm{font-size:.76rem;color:var(--g400);font-style:italic;margin-top:2px}.qcf{display:inline-block;padding:1px 8px;border-radius:4px;background:var(--r0);color:var(--r6);font-size:.72rem;font-weight:700;margin-top:3px}
        .qph{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}.qph img{width:64px;height:48px;object-fit:cover;border-radius:6px;border:1px solid var(--g200);cursor:pointer;transition:transform .2s}.qph img:hover{transform:scale(1.05)}
        .gg{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:12px 18px}.gt{position:relative;padding-top:75%;border-radius:8px;overflow:hidden;cursor:pointer;background:var(--g100)}.gt img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .3s}.gt:hover img{transform:scale(1.05)}.gt .cap{position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,.6));color:#fff;font-size:.65rem;padding:16px 8px 6px;font-weight:600}
        .lb{display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:1000;align-items:center;justify-content:center;flex-direction:column}.lb.on{display:flex}.lbc{position:absolute;top:16px;{{$isRtl?'left':'right'}}:16px;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.15);color:#fff;border:none;font-size:1.3rem;cursor:pointer;display:flex;align-items:center;justify-content:center}.lbi{max-width:92vw;max-height:80vh;border-radius:8px;object-fit:contain}.lbt{color:rgba(255,255,255,.8);font-size:.82rem;margin-top:10px;text-align:center;max-width:90vw}.lbn{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.15);color:#fff;border:none;font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center}.lbn:hover{background:rgba(255,255,255,.25)}.lbp { {{ $isRtl ? 'right' : 'left' }}: 12px; }
.lbx { {{ $isRtl ? 'left' : 'right' }}: 12px; }.lbk{color:rgba(255,255,255,.5);font-size:.72rem;margin-top:6px}
        .dl{display:block;padding:14px;background:var(--b9);color:#fff;text-align:center;border-radius:var(--rad);text-decoration:none;font-weight:700;font-size:.95rem;margin-top:16px;box-shadow:var(--sh);transition:opacity .2s}.dl:hover{opacity:.9}
        .ft{text-align:center;padding:20px 0;font-size:.72rem;color:var(--g400);line-height:1.8}
        @media(max-width:500px){.stats{grid-template-columns:repeat(2,1fr)}.stats .st:last-child{grid-column:span 2}.gg{grid-template-columns:repeat(2,1fr)}.il{width:85px}}
    </style>
</head>
<body>
<div class="topbar"><div class="topbar-in"><div class="brand">@if($logoBase64)<img src="{{$logoBase64}}" alt="">@endif<span class="brand-n">{{$company['name']}}</span></div><span class="ref-tag">{{$inspection->reference_number}}</span></div></div>
<div class="ctn">
    <div class="hero">
        <div class="vname">{{$inspection->vehicle->year}} {{$inspection->vehicle->make}} {{$inspection->vehicle->model}}</div>
        <div class="vsub">@if($inspection->vehicle->license_plate){{$inspection->vehicle->license_plate}} &bull; @endif @if($inspection->vehicle->color){{$inspection->vehicle->color}} &bull; @endif {{$inspection->vehicle->vin??''}}</div>
        @if($isScored && $inspection->percentage !== null)
        <div class="gr"><svg viewBox="0 0 110 110"><circle class="bg" cx="55" cy="55" r="50"/><circle class="fg" cx="55" cy="55" r="50"/></svg><div class="in"><span class="pct">{{number_format($inspection->percentage,0)}}<span class="ps">%</span></span></div></div>
        @if($gradeStr)<div class="gl">{{$gradeLabel}}</div>@endif
        @else
        <div class="dbadge">📝 {{$isRtl?'تقرير فحص وصفي':'Descriptive Report'}}</div>
        @endif
        <div class="dateline">{{$isRtl?'الفاحص':'Inspector'}}: {{$inspection->inspector->name??'—'}} &bull; {{$inspection->completed_at?->format('Y-m-d')}}</div>
    </div>

    @if($inspection->has_critical_failure)<div class="alert-c">⚠ {{$isRtl?'تحذير: يوجد إخفاق حرج يتطلب إصلاحاً فورياً':'Warning: Critical failures detected'}}</div>@endif

    @if($isScored)
    <div class="stats">
        <div class="st"><div class="st-l">{{$isRtl?'الدرجة':'Score'}}</div><div class="st-v">{{number_format($inspection->total_score??0,1)}}</div></div>
        <div class="st"><div class="st-l">{{$isRtl?'النسبة':'Pct'}}</div><div class="st-v">{{number_format($inspection->percentage??0,1)}}%</div></div>
        <div class="st"><div class="st-l">{{$isRtl?'حرج':'Critical'}}</div><div class="st-v" style="color:{{$inspection->has_critical_failure?'var(--r6)':'var(--g6)'}}">{{$inspection->has_critical_failure?($isRtl?'نعم':'Yes'):($isRtl?'لا':'No')}}</div></div>
    </div>
    @endif

    <div class="card"><div class="ch">🚗 {{$isRtl?'معلومات المركبة':'Vehicle'}}</div><div class="cb">
        <div class="ir"><div class="il">{{$isRtl?'المركبة':'Vehicle'}}</div><div class="iv">{{$inspection->vehicle->make}} {{$inspection->vehicle->model}} {{$inspection->vehicle->year}}</div></div>
        <div class="ir"><div class="il">{{$isRtl?'اللوحة':'Plate'}}</div><div class="iv">{{$inspection->vehicle->license_plate??'—'}}</div></div>
        <div class="ir"><div class="il">{{$isRtl?'الشاسيه':'VIN'}}</div><div class="iv" style="font-size:.8rem;word-break:break-all">{{$inspection->vehicle->vin??'—'}}</div></div>
        <div class="ir"><div class="il">{{$isRtl?'الكيلومتر':'Mileage'}}</div><div class="iv">{{$inspection->vehicle->mileage?number_format($inspection->vehicle->mileage).' km':'—'}}</div></div>
        <div class="ir"><div class="il">{{$isRtl?'المالك':'Owner'}}</div><div class="iv">{{$inspection->vehicle->owner_name??'—'}}</div></div>
    </div></div>

    @foreach($sectionResults as $sectionId => $data)
    @php $section=$data['section'];$results=$data['results']; @endphp
    <div class="card">
        <div class="sh" onclick="toggleSec('s{{$loop->index}}')"><span>{{$isRtl?'القسم':'Section'}} {{$loop->iteration}} — {{$section->name}}</span><span class="sa" id="arrow-s{{$loop->index}}">▾</span></div>
        <div class="sb" id="s{{$loop->index}}">
        @foreach($results as $item)
            @php
                $question=$item['question'];$result=$item['result'];
                $qType=is_object($question->type)?$question->type->value:$question->type;
                $isScorable=$isScored&&!in_array($qType,['text','photo'])&&$question->max_score>0;
                $score=$result?->score??0;$maxScore=$question->max_score;
                $pct=($isScorable&&$maxScore>0)?($score/$maxScore)*100:-1;
                $pc=$pct>=75?'ph':($pct>=50?'pm':'pl');
            @endphp
            <div class="qr">
                <div class="qt"><div class="ql">@if($question->is_critical)<span class="cd"></span>@endif {{$question->label}}</div>@if($isScorable&&$result)<span class="qp {{$pc}}">{{number_format($score,1)}}/{{intval($maxScore)}}</span>@endif</div>
                <div class="qa">
                    @if($result)
                        @if($qType==='checkbox') {{$result->answer=='1'?($isRtl?'✅ نعم':'✅ Yes'):($isRtl?'☐ لا':'☐ No')}}
                        @elseif($qType==='photo') <span style="color:var(--g400);font-size:.78rem">📷 {{$isRtl?'صور':'Photos'}}</span>
                        @else {{$result->answer??'—'}}
                        @endif
                    @else <span style="color:var(--g400)">—</span>
                    @endif
                </div>
                @if($result?->remarks)<div class="qrm">💬 {{$result->remarks}}</div>@endif
                @if($result?->is_critical_fail)<div class="qcf">{{$isRtl?'⚠ إخفاق حرج':'⚠ Critical Fail'}}</div>@endif
                @if($result && $result->media && $result->media->count())
                <div class="qph">@foreach($result->media as $m)@if($m->isImage())<img src="{{$m->url}}" alt="{{$m->original_name}}" onclick="openLB({{$allPhotos->search(fn($p)=>$p['url']===$m->url)?:0}})">@endif @endforeach</div>
                @endif
            </div>
        @endforeach
        </div>
    </div>
    @endforeach

    @if($allPhotos->count())
    <div class="card"><div class="ch">📷 {{$isRtl?'معرض الصور':'Photo Gallery'}} <span style="color:var(--g400);font-weight:400;font-size:.8rem;margin-{{$isRtl?'right':'left'}}:6px">({{$allPhotos->count()}})</span></div>
        <div class="gg">@foreach($allPhotos as $i=>$photo)<div class="gt" onclick="openLB({{$i}})"><img src="{{$photo['url']}}" alt="{{$photo['name']}}" loading="lazy"><div class="cap">{{$photo['question']}}</div></div>@endforeach</div>
    </div>
    @endif

    @if($inspection->notes)<div class="card"><div class="ch">📝 {{$isRtl?'ملاحظات':'Notes'}}</div><div class="cb" style="font-size:.88rem;color:var(--g500)">{{$inspection->notes}}</div></div>@endif

    <a href="{{route('share.pdf',$token)}}" class="dl">📄 {{$isRtl?'تحميل التقرير PDF':'Download PDF Report'}}</a>
    <div class="ft">{{$company['name']}} @if($company['phone'])&bull; {{$company['phone']}}@endif @if($company['website'])&bull; {{$company['website']}}@endif<br>{{$isRtl?'تم إنشاء التقرير بتاريخ':'Generated'}} {{$inspection->completed_at?->format('Y-m-d H:i')}}</div>
</div>

<div class="lb" id="lb">
    <button class="lbc" onclick="closeLB()">✕</button>
    <button class="lbn lbp" onclick="navLB(-1)">{{$isRtl?'›':'‹'}}</button>
    <button class="lbn lbx" onclick="navLB(1)">{{$isRtl?'‹':'›'}}</button>
    <img class="lbi" id="lbi" src="" alt="">
    <div class="lbt" id="lbt"></div>
    <div class="lbk" id="lbk"></div>
</div>
<script>
var P=@json($allPhotos->values()),I=0;
function openLB(i){I=i;rLB();document.getElementById('lb').classList.add('on');document.body.style.overflow='hidden'}
function closeLB(){document.getElementById('lb').classList.remove('on');document.body.style.overflow=''}
function navLB(d){I=(I+d+P.length)%P.length;rLB()}
function rLB(){var p=P[I];document.getElementById('lbi').src=p.url;document.getElementById('lbt').textContent=p.question+' — '+p.section;document.getElementById('lbk').textContent=(I+1)+' / '+P.length}
function toggleSec(id){var e=document.getElementById(id),a=document.getElementById('arrow-'+id);if(e.style.display==='none'){e.style.display='block';a.style.transform='rotate(0)'}else{e.style.display='none';a.style.transform='rotate(-90deg)'}}
document.addEventListener('keydown',function(e){if(!document.getElementById('lb').classList.contains('on'))return;if(e.key==='Escape')closeLB();if(e.key==='ArrowLeft')navLB({{$isRtl?'1':'-1'}});if(e.key==='ArrowRight')navLB({{$isRtl?'-1':'1'}})});
var tX=0;document.getElementById('lb').addEventListener('touchstart',function(e){tX=e.touches[0].clientX});document.getElementById('lb').addEventListener('touchend',function(e){var d=e.changedTouches[0].clientX-tX;if(Math.abs(d)>50)navLB(d>0?{{$isRtl?'1':'-1'}}:{{$isRtl?'-1':'1'}})});
</script>
</body>
</html>