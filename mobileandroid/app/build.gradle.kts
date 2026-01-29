plugins {
    alias(libs.plugins.android.application)
    alias(libs.plugins.kotlin.android)
    id("kotlin-kapt")
    // Firebaseは使用しないためコメントアウト
    // id("com.google.gms.google-services")
}

android {
    namespace = "ecccomp.s2240788.mobile_android"
    compileSdk = 36

    defaultConfig {
        applicationId = "ecccomp.s2240788.mobile_android"
        minSdk = 24
        targetSdk = 35
        versionCode = 1
        versionName = "1.0"

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
    }

    buildTypes {
        release {
            isMinifyEnabled = false
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
            // デバッグキーで署名（開発・テスト用）
            // 本番環境では適切なキーストアを使用してください
            signingConfig = signingConfigs.getByName("debug")
        }
        debug {
            // Debugビルドは自動的にデバッグキーで署名される
        }
    }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
    kotlinOptions {
        jvmTarget = "17"
    }

    buildFeatures {
        viewBinding = true
        dataBinding = true
    }
}

// Dependencies cho TodoApp AI
dependencies {
    // Core Android (sử dụng libs.versions.toml)
    implementation(libs.androidx.core.ktx)
    implementation(libs.androidx.appcompat)
    implementation(libs.material)
    implementation(libs.androidx.activity)
    implementation(libs.androidx.constraintlayout)

    // Architecture Components
    implementation(libs.androidx.lifecycle.viewmodel.ktx)
    implementation(libs.androidx.lifecycle.livedata.ktx)
    implementation(libs.androidx.fragment)
    implementation(libs.androidx.lifecycle.viewmodel.android)

    // Navigation Component
    implementation(libs.androidx.navigation.fragment)
    implementation(libs.androidx.navigation.ui)

    // SwipeRefreshLayout
    implementation("androidx.swiperefreshlayout:swiperefreshlayout:1.1.0")

    // Animation & Transitions
    implementation(libs.androidx.dynamicanimation)
    implementation(libs.androidx.transition)

    // Networking (Retrofit + OkHttp)
    implementation(libs.retrofit)
    implementation(libs.retrofit.gson)
    implementation(libs.okhttp.logging)
    implementation(libs.okhttp)

    // Coroutines
    implementation(libs.kotlinx.coroutines.android)
    implementation(libs.kotlinx.coroutines.core)

    // Room Database (cho offline support)
    implementation(libs.androidx.room.runtime)
    implementation(libs.androidx.room.ktx)
    kapt(libs.androidx.room.compiler)

    // Security (cho token storage)
    implementation(libs.androidx.security.crypto)

    // Image Loading (nếu cần avatar)
    implementation(libs.glide)
    kapt(libs.glide.compiler)

    // JSON Processing
    implementation(libs.gson)

    // WorkManager for background tasks
    implementation("androidx.work:work-runtime-ktx:2.9.0")

    // Firebaseは使用しないためコメントアウト
    // Firebase Cloud Messaging (FCM) for push notifications
    // implementation(platform("com.google.firebase:firebase-bom:34.6.0"))
    // implementation("com.google.firebase:firebase-messaging")
    // implementation("com.google.firebase:firebase-analytics")

    // Testing
    testImplementation(libs.junit)
    testImplementation(libs.kotlinx.coroutines.test)
    testImplementation(libs.androidx.room.testing)
    androidTestImplementation(libs.androidx.junit)
    androidTestImplementation(libs.androidx.espresso.core)
    androidTestImplementation(libs.androidx.navigation.testing)
}