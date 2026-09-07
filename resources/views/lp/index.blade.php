@extends('lp.layouts.app')

@section('content')

<!-- cover -->
<div id="cover" class="mv_point">
	<div class="bg_sou1"><img src="{{ asset('lp/img/' ) }}/top/mv_sou1.svg" alt=""></div>
	<div class="bg_sou2"><img src="{{ asset('lp/img/' ) }}/top/mv_sou2.svg" alt=""></div>
	<div class="bg_sou3"><img src="{{ asset('lp/img/' ) }}/top/mv_sou3.svg" alt=""></div>
	<div class="bg_sou4"><img src="{{ asset('lp/img/' ) }}/top/mv_sou4.svg" alt=""></div>
	<div class="bg_sou5"><img src="{{ asset('lp/img/' ) }}/top/mv_sou5.svg" alt=""></div>
	<div class="text_area">
		<p class="text1"><span>あっというまに<span>口コミ</span>が増える！</span></p>
		<h2 class="title1"><img src="{{ asset('lp/img/' ) }}/top/mv_tit.svg" alt="簡単アンケートが口コミに変わる"></h2>
		<p class="text2">AIによる文章生成サポート</p>
		<a href="{{ url('/register') }}" class="mv_bnr1">
			<div class="mv_btn_img"><img src="{{ asset('lp/img/' ) }}/top/mv_btn.svg" alt=""></div>
			<p class="btn1_text1 white">
				<span class="yellow">まずは無料でお試し！</span>
				新規登録はこちら
			</p>
			<p class="btn1_text2 bgyel blue">無料</p>
		</a>
	</div>
	<div class="img_slider_area">

		<!-- Blob本体 -->
		<div class="blob_wrap">
	
			<!-- SVGマスク -->
			<svg class="mask_svg">
				<defs>
					<clipPath id="blobClip">
						<path id="blobPath"></path>
					</clipPath>
				</defs>
			</svg>
	
			<!-- Slick -->
			<div class="img_slider">
	
				<div><img src="{{ asset('lp/img/' ) }}/top/mv_sld1.jpg" alt=""></div>
				<div><img src="{{ asset('lp/img/' ) }}/top/mv_sld2.jpg" alt=""></div>
	
			</div>
	
		</div>
	
		<!-- 装飾 -->
		<div class="mv_img_sou1">
			<img src="{{ asset('lp/img/' ) }}/top/mv_img_sou1.png" alt="">
		</div>
	
		<div class="mv_img_sou2">
			<img src="{{ asset('lp/img/' ) }}/top/mv_img_sou2.png" alt="">
		</div>
	
		<div class="mv_img_sou3">
			<img src="{{ asset('lp/img/' ) }}/top/mv_img_sou3.png" alt="">
		</div>
	
	</div>
</div>
<!-- /cover -->


<div id="main">
	<section class="content01">
		<div class="ma1920">
			<div class="sou1"><img src="{{ asset('lp/img/' ) }}/top/co1_sou1.svg" alt=""></div>
			<div class="sou2"><img src="{{ asset('lp/img/' ) }}/top/co1_sou2.svg" alt=""></div>
			<div class="inner">
				<h2 class="mainTit">
					たったこれだけで<br class="sp2">口コミができる！
					<span class="sub">簡単操作で口コミの文章作成から投稿まで、<br class="sp">お客様をサポートします</span>
				</h2>
				<ul class="co1_list1">
					<li class="cont">
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co11_1.png" alt="icon"></div>
						<h3 class="title1 green">QRを読み込む</h3>
						<p class="text1">
							QRを読み込むだけで<br>
							簡単にアンケートにアクセス可能
						</p>
					</li>
					<li class="cont">
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co11_2.png" alt="icon"></div>
						<h3 class="title1 green">アンケートに回答</h3>
						<p class="text1">
							アンケートに回答するだけで<br>
							AIが文章を自動に生成
						</p>
					</li>
					<li class="cont">
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co11_3.png" alt="icon"></div>
						<h3 class="title1 green">口コミ完成！</h3>
						<p class="text1">
							できあがった文章を投稿するだけ！<br>
							もちろん書き直しも可能
						</p>
					</li>
				</ul>
				<div class="co1_div1">
					<p class="text1 blue">「お客様の負担をできるだけ軽減する」<br class="sp">これが口コミ獲得への近道です！</p>
				</div>
				<div class="co1_div2">
					<div class="text_area">
						<h3 class="title1">
							<span class="sub">Coelとは</span>
							<span class="tit"><img src="{{ asset('lp/img/' ) }}/top/co12_tit.svg" alt="お客様は選ぶだけ。口コミ投稿をもっと簡単に。"></span>
						</h3>
						<p class="text1">
							Coelは、AIを活用した口コミ作成サポートサービスです。<br>
							<br>
							口コミ投稿は、お客様にとって意外と手間のかかるもの。<br>
							Coelなら、アンケートに回答するだけでAIが自然な<br>
							口コミ文を生成し、投稿までをサポートします。<br>
							<br>
							口コミ投稿のハードルを下げることで、店舗の口コミ獲得を支援します。
						</p>
					</div>
					<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co12_1.png" alt=""></div>
				</div>
			</div>
		</div>
	</section>

	<section class="content02">
		<div class="inner2">
			<div class="co2_div1 white">
				<h2 class="mainTit">
					こんなお悩み<br class="sp2">ありませんか？
				</h2>
				<ul class="co2_list1">
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co21_1.svg" alt="icon"></div>
						<p class="text1">
							口コミの数が少ない<br>
							増えない
						</p>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co21_2.svg" alt="icon"></div>
						<p class="text1">
							口コミの内容が簡素で<br>
							魅力が伝わらない
						</p>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co21_3.svg" alt="icon"></div>
						<p class="text1">
							口コミが管理できていない
						</p>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co21_4.svg" alt="icon"></div>
						<p class="text1">
							口コミは欲しいけど<br>
							お客様に面倒をかけたくない
						</p>
					</li>
				</ul>
			</div>
			<div class="co2_div2">
				<div class="dot1"><img src="{{ asset('lp/img/' ) }}/top/co2_dot1.svg" alt="dot"></div>
				<p class="text1">その悩み、口コミを書く<br class="sp">“ハードル”が原因かもしれません。</p>
				<div class="dot2"><img src="{{ asset('lp/img/' ) }}/top/co2_dot2.svg" alt="dot"></div>
				<h3 class="title1">
					<span class="green">Coelなら、お客様は選ぶだけ。</span>
					AIが自然な口コミ文に整えます。
				</h3>
				<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co22_2.png" alt="CoelはAI技術を使って、魅力的な口コミの作成をサポート"></div>
				<p class="text2">
					お客様のお店の認知度の<br class="sp">向上サービス改善を目指します
				</p>
			</div>
		</div>
	</section>

	<section class="content03">
		<div class="inner2">
			<div class="co3_div1">
				<h2 class="mainTit">
					<span class="green2">Coel</span>が選ばれる理由
				</h2>
				<ul class="co3_list1">
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co31_1.png" alt=""></div>
						<h3 class="title1 green">
							回答率の高い選択式で<br>
							口コミを生成
						</h3>
						<p class="text1">
							アンケートの質問形式は自由に設定することができます。<br>
							文章形式から選択形式まで、さまざまなタイプをご用意しています。
						</p>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co31_2.png" alt=""></div>
						<h3 class="title1 green">
							さまざまな業種に合わせやすい<br>
							柔軟な設計
						</h3>
						<p class="text1">
							アンケートの質問形式は自由に設定することができます。<br>
							文章形式から選択形式まで、さまざまなタイプをご用意しています。
						</p>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co31_3.png" alt=""></div>
						<h3 class="title1 green">
							QRでかんたん！<br>
							導入時に店内のQRポップを<br class="sp2">プレゼント
						</h3>
						<p class="text1">
							店内に設置するアクセス用のQRポップも、デザインから印刷まで含めてプレゼント！<br>
							スムーズな導入でいち早く口コミをゲットしましょう。
						</p>
					</li>
				</ul>
				<a href="{{ url('/register') }}" class="btn1">
					<p class="btn1_text1 white">
						<span class="yellow">まずは無料でお試し！</span>
						新規登録はこちら
					</p>
					<p class="btn1_text2 bgwh blue">無料</p>
				</a>
			</div>
		</div>
		<div class="inner">
			<div class="co3_div2">
				<h2 class="mainTit">
					Coelが選ばれる理由
					<span class="sub">主なサポート機能をご紹介します</span>
				</h2>
				<ul class="co3_list2">
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co32_1.svg" alt="icon"></div>
						<div class="text_area">
							<p class="title1">管理画面での一括管理</p>
							<p class="text1">
								分かりやすい管理画面で簡単管理
							</p>
						</div>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co32_2.svg" alt="icon"></div>
						<div class="text_area">
							<p class="title1">口コミ一覧</p>
							<p class="text1">
								口コミを一覧で管理できるので、バラバラになった情報を集める必要がありません。
							</p>
						</div>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co32_3.svg" alt="icon"></div>
						<div class="text_area">
							<p class="title1">アンケート作成</p>
							<p class="text1">
								お店のサービスにあわせて設問の設定が可能です。
							</p>
						</div>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co32_4.svg" alt="icon"></div>
						<div class="text_area">
							<p class="title1">AIによる文章生成</p>
							<p class="text1">
								簡単なアンケートに答えるだけで、魅力的な文章が生成されます。
							</p>
						</div>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co32_5.svg" alt="icon"></div>
						<div class="text_area">
							<p class="title1">投稿画面への誘導</p>
							<p class="text1">
								生成された文章を1タップするだけでコピー、投稿画面まで誘導が可能です。
							</p>
						</div>
					</li>
					<li>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co32_6.svg" alt="icon"></div>
						<div class="text_area">
							<p class="title1">QR発行機能</p>
							<p class="text1">
								管理画面からQRが発行できるので、SNSや店頭、チラシにも載せることができます。
							</p>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<section class="content04">
		<div class="co4_div1">
			<div class="inner">
				<h2 class="mainTit">
					導入実績
				</h2>
				<div class="co4_slider_area">
					<div class="co4_slider">
						<div class="co4_sld_cont">
							<div class="co4_sld">
								<a href="./works_detail" class="lista"></a>
								<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co41_1.jpg" alt="slider"></div>
								<ul class="cate_list1">
									<li>飲食店</li>
								</ul>
								<p class="text1">
									導入までがスムーズでした！<br>
									口コミも増えてきたので、これからのお店の運営に活かしたいと思います。
								</p>
							</div>
						</div>
						<div class="co4_sld_cont">
							<div class="co4_sld">
								<a href="./works_detail" class="lista"></a>
								<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co41_2.jpg" alt="slider"></div>
								<ul class="cate_list1">
									<li>美容室</li>
								</ul>
								<p class="text1">
									カラー待ちの間に入力してもらえるのでとても助かります。<br>
									「AIで生成できるんですよ」と伝えると、施術中の話題にもなって一石二鳥です。
								</p>
							</div>
						</div>
						<div class="co4_sld_cont">
							<div class="co4_sld">
								<a href="./works_detail" class="lista"></a>
								<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co41_3.jpg" alt="slider"></div>
								<ul class="cate_list1">
									<li>ネイルサロン</li>
								</ul>
								<p class="text1">
									片手で簡単に口コミが作れるので、施術中でも対応してもらえます。<br>
									硬化中の1分で口コミができるのはすごいですね。
								</p>
							</div>
						</div>
						<div class="co4_sld_cont">
							<div class="co4_sld">
								<a href="./works_detail" class="lista"></a>
								<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co41_1.jpg" alt="slider"></div>
								<ul class="cate_list1">
									<li>飲食店</li>
								</ul>
								<p class="text1">
									導入までがスムーズでした！<br>
									口コミも増えてきたので、これからのお店の運営に活かしたいと思います。
								</p>
							</div>
						</div>
						<div class="co4_sld_cont">
							<div class="co4_sld">
								<a href="./works_detail" class="lista"></a>
								<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co41_2.jpg" alt="slider"></div>
								<ul class="cate_list1">
									<li>美容室</li>
								</ul>
								<p class="text1">
									カラー待ちの間に入力してもらえるのでとても助かります。<br>
									「AIで生成できるんですよ」と伝えると、施術中の話題にもなって一石二鳥です。
								</p>
							</div>
						</div>
						<div class="co4_sld_cont">
							<div class="co4_sld">
								<a href="./works_detail" class="lista"></a>
								<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co41_3.jpg" alt="slider"></div>
								<ul class="cate_list1">
									<li>ネイルサロン</li>
								</ul>
								<p class="text1">
									片手で簡単に口コミが作れるので、施術中でも対応してもらえます。<br>
									硬化中の1分で口コミができるのはすごいですね。
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="co4_div2">
			<div class="inner">
				<div class="co4_div21">
					<h2 class="mainTit white">
						Coelは様々な業種で<br class="sp2">導入可能！
					</h2>
					<ul class="co4_list2">
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_1.png" alt="業種"></div>
							<p class="text1">レストラン</p>
						</li>
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_2.png" alt="業種"></div>
							<p class="text1">美容院</p>
						</li>
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_3.png" alt="業種"></div>
							<p class="text1">ネイルサロン</p>
						</li>
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_4.png" alt="業種"></div>
							<p class="text1">小売店</p>
						</li>
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_5.png" alt="業種"></div>
							<p class="text1">ライブハウス</p>
						</li>
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_6.png" alt="業種"></div>
							<p class="text1">カフェ</p>
						</li>
						<li>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co42_7.png" alt="業種"></div>
							<p class="text1">観光</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>


	<section class="content05">
		<div class="inner">
			<h2 class="mainTit">
				<span class="sub">導入しやすい！続けやすい！</span>
				低価格プラン
			</h2>
			<ul class="co05_list1">
				<li>
					<h3 class="title1">
						お試しプラン
					</h3>
					<p class="price">
						<span class="mid">¥</span>0<span class="small">/ 月</span>
					</p>
					<ul class="co05_list2">
						<li>口コミ5件までのお試しプラン</li>
						<li>その他機能はスタンダードプランと同じ</li>
					</ul>
					<div class="co5_div1">
						<p class="text1">※QRポッププレゼント対象外となります</p>
					</div>
				</li>
				<li>
					<h3 class="title1">
						スタンダードプラン
					</h3>
					<p class="price">
						<span class="mid">¥</span>3<span class="small2">,</span>000<span class="small">/ 月</span>
					</p>
					<ul class="co05_list2">
						<li>初期費用・追加費用なし</li>
						<li>口コミ件数制限なし</li>
						<li>設問は50件まで登録可能</li>
						<li>導入時のQRポップをプレゼント</li>
					</ul>
				</li>
				<li>
					<h3 class="title1">
						プレミアムプラン
					</h3>
					<p class="price2">
						Comingsoon
					</p>
					<p class="text2">より便利な機能を開発中！</p>
				</li>
			</ul>
			<p class="text">※金額は税抜き表記です</p>
			<a href="{{ url('/register') }}" class="btn1">
				<p class="btn1_text1 white">
					<span class="yellow">まずは無料でお試し！</span>
					新規登録はこちら
				</p>
				<p class="btn1_text2 bgwh blue">無料</p>
			</a>
		</div>
	</section>

	<section class="content06">
		<div class="ma1920">
			<div class="sou1"><img src="{{ asset('lp/img/' ) }}/top/co6_sou1.svg" alt=""></div>
			<div class="co6_div">
				<div class="inner">
					<h2 class="mainTit">
						ご利用の流れ
					</h2>
					<ul class="co6_list1">
						<li class="cont">
							<p class="step en green">STEP 01</p>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co61_1.png" alt="icon"></div>
							<p class="title1 green">ご利用申請</p>
							<p class="text1">
								当サイトのご利用申請よりご連絡ください。
							</p>
						</li>
						<li class="arrow"></li>
						<li class="cont">
							<p class="step en green">STEP 02</p>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co61_2.png" alt="icon"></div>
							<p class="title1 green">ご契約</p>
							<p class="text1">
								ご契約に必要な情報をご連絡いただきます。
							</p>
						</li>
						<li class="arrow"></li>
						<li class="cont">
							<p class="step en green">STEP 03</p>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co61_3.png" alt="icon"></div>
							<p class="title1 green">アカウント・初期設定</p>
							<p class="text1">
								アカウント・初期設定を行っていただきます。
							</p>
						</li>
						<li class="arrow"></li>
						<li class="cont">
							<p class="step en green">STEP 04</p>
							<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co61_4.png" alt="icon"></div>
							<p class="title1 green">ご利用開始</p>
							<p class="text1">
								たくさん口コミを集めましょう！
							</p>
						</li>
						<li class="arrow none"></li>
					</ul>
					<div class="co6_div1">
						<h3 class="title1 green">QRポップの制作について</h3>
						<p class="text1">
							デザインをヒアリング後、デザイン・印刷を行います。<br>
							進行によってはCoel導入後の発送となる場合がございます。
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>



	<section class="content07">
		<div class="ma1920">
			<div class="sou1"><img src="{{ asset('lp/img/' ) }}/top/co7_sou1.svg" alt=""></div>
			<div class="inner">
				<h2 class="mainTit">
					よくある質問
				</h2>
				<ul class="qanda_list">
					<li>
						<dl>
							<dt>
								<span class="que en">Q.</span>
								<p class="title1">文章が似通ったりはしませんか？</p>
								<div class="pulu">
									<span></span>
									<span></span>
								</div>
							</dt>
							<dd>
								<span class="ans en">A.</span>
								<p class="text1">
									テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
								</p>
							</dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt>
								<span class="que en">Q.</span>
								<p class="title1">操作は簡単ですか？</p>
								<div class="pulu">
									<span></span>
									<span></span>
								</div>
							</dt>
							<dd>
								<span class="ans en">A.</span>
								<p class="text1">
									テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
								</p>
							</dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt>
								<span class="que en">Q.</span>
								<p class="title1">口コミは何件まで保存できますか？</p>
								<div class="pulu">
									<span></span>
									<span></span>
								</div>
							</dt>
							<dd>
								<span class="ans en">A.</span>
								<p class="text1">
									テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
								</p>
							</dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt>
								<span class="que en">Q.</span>
								<p class="title1">契約解消後、管理画面の口コミはどうなりますか？</p>
								<div class="pulu">
									<span></span>
									<span></span>
								</div>
							</dt>
							<dd>
								<span class="ans en">A.</span>
								<p class="text1">
									テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
								</p>
							</dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt>
								<span class="que en">Q.</span>
								<p class="title1">解約した場合はいつからできますか？</p>
								<div class="pulu">
									<span></span>
									<span></span>
								</div>
							</dt>
							<dd>
								<span class="ans en">A.</span>
								<p class="text1">
									テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
								</p>
							</dd>
						</dl>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- <section class="content08">
		<div class="ma1920">
			<div class="sou1"><img src="{{ asset('lp/img/' ) }}/top/co8_sou1.svg" alt=""></div>
			<div class="inner">
				<h2 class="mainTit">
					コラム
				</h2>
				<ul class="co8_list1">
					<li>
						<a href="./column_detail" class="lista"></a>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co81_1.jpg" alt="コラム"></div>
						<div class="dacate">
							<p class="date">2026.00.00</p>
							<p class="cate">カテゴリ</p>
						</div>
						<p class="text1">
							テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。
						</p>
					</li>
					<li>
						<a href="./column_detail" class="lista"></a>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co81_2.jpg" alt="コラム"></div>
						<div class="dacate">
							<p class="date">2026.00.00</p>
							<p class="cate">カテゴリ</p>
						</div>
						<p class="text1">
							テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。
						</p>
					</li>
					<li>
						<a href="./column_detail" class="lista"></a>
						<div class="img"><img src="{{ asset('lp/img/' ) }}/top/co81_3.jpg" alt="コラム"></div>
						<div class="dacate">
							<p class="date">2026.00.00</p>
							<p class="cate">カテゴリ</p>
						</div>
						<p class="text1">
							テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。
						</p>
					</li>
				</ul>
			</div>
		</div>
	</section> -->

	<div class="content08">
		<div class="ma1920">
			<div class="inner">
				@include('lp.components.notices')
			</div>
		</div>
	</div>


	<section class="content09">
		<div class="inner">
			<h2 class="mainTit white">
				<span class="sub">口コミは、店を変える力になる</span>
				Coelで今すぐ口コミを獲得！
			</h2>
			<div class="btn_flex1">
				<a href="{{ url('/register') }}" class="btn1">
					<p class="btn1_text1 white">
						<span class="yellow">まずは無料でお試し！</span>
						新規登録はこちら
					</p>
					<p class="btn1_text2 bgwh blue">無料</p>
				</a>
				<a href="{{ url('/contact') }}" class="btn2 green bgwh">その他お問い合わせ</a>
			</div>
		</div>
	</section>


</div><!-- /#main -->


<div class="side_bnr_area">
	<div class="side_close_btn"><img src="{{ asset('lp/img/' ) }}/common/side_close.svg" alt="close"></div>
	<a href="{{ url('/register') }}" class="side_bnr"><img src="{{ asset('lp/img/' ) }}/common/side_bnr.png" alt="ご協力いただいたご契約者様限定 利用料３ヶ月無料キャンペーン"></a>
	<p class="text1">※定員に達し次第キャンペーン終了となります</p>
</div>

@endsection
