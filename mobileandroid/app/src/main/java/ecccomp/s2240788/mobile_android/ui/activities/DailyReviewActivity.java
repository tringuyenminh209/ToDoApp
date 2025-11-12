package ecccomp.s2240788.mobile_android.ui.activities;

import android.os.Bundle;
import ecccomp.s2240788.mobile_android.databinding.ActivityDailyReviewBinding;

public class DailyReviewActivity extends BaseActivity {
    
    private ActivityDailyReviewBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDailyReviewBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());


    }
}
