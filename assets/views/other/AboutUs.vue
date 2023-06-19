<template>
  <base-page>
    <div class="wrapper-item">
      <base-image src="/images/logo.png" :size="16"></base-image>

      <h1 class="header-item">Веб-Сайт Библиотека МТЭК</h1>
      <h3 class="header-item header-text">
        Веб-Сайт разработан Лауренчикасом Марком Павловичем <el-tag style="text-size: 1.5rem">Группы ПО409</el-tag>
      </h3>

      <div class="description">
        <p>
          Наша библиотека представляет собой уникальный источник знаний и информации, доступный каждому
          интернет-пользователю. Наш сайт разработан для того, чтобы помочь вам находить и получать доступ к широкому
          ассортименту книг.
        </p>
        <p>
          Главная страница сайта библиотеки предлагает вам простую и удобную навигацию, позволяющую легко найти
          интересующие вас материалы. Вы можете воспользоваться поиском, чтобы быстро найти нужную книгу или автора, или
          воспользоваться разделами и категориями, чтобы исследовать различные тематические области.
        </p>
        <p>
          Мы предлагаем широкий выбор жанров и тематик, чтобы удовлетворить разнообразные интересы наших читателей. На
          данный момент библиотека включает в себя <el-tag type="success">58.000</el-tag> экземпляров. От классической
          литературы до современных бестселлеров, от научно-популярных книг до технической литературы - у нас вы найдете
          все, что нужно для развлечения, саморазвития и образования.
        </p>
      </div>
    </div>
    <el-carousel :interval="5000" arrow="always" height="40rem">
      <el-carousel-item name="1">
        <img src="/images/photos/library-1.jpg" />
      </el-carousel-item>
      <el-carousel-item name="2">
        <img src="/images/photos/library-2.jpg" />
      </el-carousel-item>
      <el-carousel-item name="3">
        <img src="/images/photos/library-3.jpg" />
      </el-carousel-item>
      <el-carousel-item name="3">
        <img src="/images/photos/library-4.jpg" />
      </el-carousel-item>
    </el-carousel>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseImage } from '@/components/tags/base';
import { ElCarousel, ElCarouselItem, ElTag } from 'element-plus';
</script>

<style scoped>
.el-carousel {
  width: 60%;
  margin: 0 auto;
}

.el-carousel__item img {
  width: auto;
  max-height: 40rem;
  margin: 0 auto;
}

.el-carousel__item {
  display: flex;
  padding: 0;
  margin: 0;
}

.carousel {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.wrapper-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header-item {
  margin: 0.5rem 0;
}

.header-text {
  text-align: center;
}

.description {
  width: 60%;
  font-size: 1.125rem;
  display: flex;
  flex-direction: column;
  text-align: justify;
}

@media only screen and (max-width: 768px) {
  .description {
    width: 90%;
  }

  .el-carousel {
    width: 90%;
  }
}
</style>
