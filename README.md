验证码组件
------

用于生成验证码，支持自定义验证码字体，使用 Composer 安装：

```
composer require easyswoole/verifycode=4.x
```

配置定义
------

组件本身提供了默认配置，即使不做任何设置也可以直接生成验证码，需要对验证码进行自定义配置可以使用组件提供的`Config`类进行动态配置

```
// v1.x
use easySwoole\VerifyCode\Config;
$config = new Config();
// v2.x
use EasySwoole\VerifyCode\Config;
$config = new Config();
```

#### 设置字符集合
可以自定义验证码生成使用的字符集合设置后从集合中随机选取，不设置则从`[0-9A-Za-z]`中随机选取

```
$config->setCharset('123456ABCD');
```

#### 设置背景色
设置验证码的背景颜色，不设置默认使用白色，支持使用完整HEX，缩写HEX和RGB值设置

```
$config->setBackColor('#3A5FCD');
$config->setBackColor('CCC');
$config->setBackColor([30, 144, 255]);
```

#### 设置文字颜色
设置验证码的背景颜色，不设置则随机生成一个颜色，支持使用完整HEX，缩写HEX和RGB值设置

```
$config->setFontColor('#3A5FCD');
$config->setFontColor('CCC');
$config->setFontColor([30, 144, 255]);
```

#### 设置混淆
支持两种混淆方式。噪点默认开启，混淆曲线默认关闭：

```
// 开启或关闭混淆曲线
$config->setUseCurve();
// 开启或关闭混淆噪点
$config->setUseNoise();
```

#### 设置字体
默认验证码类已经带有6种字体，如果需要增加自己的字体库来提高识别难度，或者指定使用的字体，可以如下设置，注意字体路径需要使用绝对路径，即文件的完整路径

内置字体均来自 Google Fonts，采用 SIL OFL 1.1 许可证，并已裁剪为只包含数字和大小写英文字母。字体来源、修改说明和完整许可证见 `src/assets/ttf/FONT-LICENSES.md`。

```
// 增加单个字体传入路径字符串
$config->addFonts('path/to/file.ttf');
// 增加多个字体传入路径的数组
$config->addFonts(['path/to/file1.ttf', 'path/to/file2.ttf']);
```

```
// 指定生成使用的字体文件
$config->setUseFont('path/to/file.ttf');
```

#### 其他设置
可以指定图片宽高，字体大小，随机生成的验证码位数等

```
// 设置图片的宽度
$config->setImageWidth(400);
// 设置图片的高度
$config->setImageHeight(200);
// 设置生成字体大小
$config->setFontSize(30);
// 设置生成验证码位数
$config->setLength(4);
```

#### 设置复杂度

复杂度范围为 1～9，默认是 1。等级越高，字符的旋转、缩放、位移和间距变化越大，同时会逐级增加噪点、干扰线和曲线：

```php
$config->setComplexityLevel(6);
```

建议登录等常规场景使用 4～6，风险较高的场景使用 7～9，并配合有效期、一次性校验和请求限流。

目标颜色、噪点、曲线和复杂度可以独立配置。复杂度只改变字符形变及已启用干扰效果的强度，不会自动开启噪点或曲线：

```php
$config
    ->setComplexityLevel(7)
    ->setUseNoise(false)
    ->setUseCurve(false);
```

#### 指定目标颜色

可以要求用户只输入指定颜色的验证码。启用后，图片中会自动加入其他颜色的干扰验证码：

```php
use EasySwoole\VerifyCode\CaptchaColor;

$config->setDecoyCodeCount(2);      // 干扰字符组数
$config->setDecoyCharacterCount(2); // 每组绘制的干扰字符数
$result = $VCode->drawCode(null, CaptchaColor::BLUE);

$result->getPrompt();      // 请输入蓝色的验证码
$result->getImageCode();   // 需要用户输入的目标验证码
$result->getTargetColor(); // CaptchaColor::BLUE
```

内置颜色包括红、蓝、绿、紫、橙、黑六种，由 `CaptchaColor` 枚举统一定义。每种目标色都有独立的高区分度干扰色集合，会避开红/绿、红/橙、蓝/紫等常见色弱混淆组合；干扰数量较多时会复用安全颜色，不会回退到相近颜色。

干扰字符不会再绘制完整验证码，而是随机选择目标字符位置进行局部重叠。默认每组只绘制 2 个字符，在减少视觉杂乱的同时破坏目标字符轮廓。

#### 链式调用
为了更流畅的进行设置，所有的配置项均支持链式调用

```
$config = new Config();
$config->setUseNoise()->setUseCurve()->setFontSize(30);
```

------

可以使用上方的动态配置，将设置好的配置类传入给验证码类，
```
$config = new Config();
$config->setFontSize(30);
$VCode = new VerifyCode($config);
```

如果配置比较多，也需要全站统一验证码配置，可以将验证码的配置放入配置文件，在生成时读取配置，验证码的设置类继承自`SplBean`，可以在设置好后使用配置类的`toArray`方法直接获得配置数组，实例化验证码时，读取数组重新生成Config类即可

生成
------

初始化配置后即可生成验证码，可以随机生成，也可以指定需要生成的验证码

```
$VCode = new VerifyCode($config);

// 随机生成验证码
$Code = $VCode->drawCode();

// 生成指定验证码
$Code = $VCode->drawCode('MyCode');
```

生成好验证码后结果是一个`Result`类，可以使用`getImageByte`获取验证码的图片内容，使用`getImageCode`获得验证码字符，`getImageMime`获得验证码的 MIME 信息。

### EasySwoole

在控制器中输出

```
$this->response()->withHeader("Content-Type",$code->getImageMime());
$this->response()->write($code->getImageByte());
```

测试
------

```bash
composer install
composer test
```
