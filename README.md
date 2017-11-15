验证码组件
------

用于生产验证码，支持自定义验证码字体

配置
------

组件本身提供了默认配置，即使不做任何设置也可以直接生成验证码，需要对验证码进行自定义配置可以使用组件提供的`Conf`类进行动态配置

```
use easySwoole\VerifyCode\Conf;

$Conf = new Conf();

// 验证码字符长度 默认生成4位
$Conf->length = 4;

// 指定字体文件 默认随机
$Conf->useFont = '/path/to/file.ttf';

// 验证码的文字颜色[R,G,B] 默认随机
$Conf->fontColor = [135, 135, 135];

// 验证码图片宽度 默认动态适应
$Conf->imageL = 400;

// 验证码图片高度 默认动态适应
$Conf->imageH = 200;

// 验证码字符集 默认数字+大小写字母
$Conf->charset = '1234567890';

// 开启干扰噪点 默认不开启
$Conf->useNoise = false;

// 开启混淆曲线 默认不开启
$Conf->useCurve = false;

// 添加字体到验证码字体库 生成时随机
$Conf->fonts = ['/path/to/file.ttf', '/path/to/file2.ttf'];

// 验证码字体大小
$Conf->fontSize = 25;

```

传入配置有两种方法，可以使用上方的动态配置，将设置好的配置类传入给验证码类

```
$Conf = new Conf();
$Conf->length = 5;
$VCode = new VerifyCode($Conf);
```

如果配置比较多，也需要全站统一验证码配置，可以将验证码的配置放入配置文件，在生成时读取配置，验证码的设置类继承自`SplBean`，可以在设置好后使用配置类的`toArray`方法直接获得配置数组，实例化验证码时，读取数组重新生成Conf类即可

生成
------

初始化配置后即可生成验证码，可以随机生成，也可以指定需要生成的验证码

```
$VCode = new VerifyCode($Conf);

// 随机生成验证码
$Code = $VCode->DrawCode();

// 生成指定验证码
$Code = $VCode->DrawCode('MyCode');
```

生成好验证码后结果是一个`Result`类，可以使用`getImageBody`获取验证码的图片内容，使用`getImageStr`获得验证码字符，`getMineType`获得验证码的Mine信息