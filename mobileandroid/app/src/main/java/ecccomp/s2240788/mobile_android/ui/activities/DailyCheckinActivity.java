package ecccomp.s2240788.mobile_android.ui.activities;

import android.os.Bundle;
import androidx.core.view.ViewCompat;
import ecccomp.s2240788.mobile_android.R;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import ecccomp.s2240788.mobile_android.databinding.ActivityDailyCheckinBinding;

public class DailyCheckinActivity extends BaseActivity {
    
    private ActivityDailyCheckinBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDailyCheckinBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());


    }
}
