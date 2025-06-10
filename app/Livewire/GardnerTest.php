<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GardnerTestResult;
use Illuminate\Support\Facades\Auth;

class GardnerTest extends Component
{
    public $currentQuestion = 0;
    public $answers = [];
    public $questions = [];
    public $totalQuestions = 56; // 7 вопросов на каждый из 8 типов интеллекта
    public $isCompleted = false;
    public $results = [];

    public function mount()
    {
        // Проверяем, есть ли уже результаты теста для пользователя
        $existingResult = GardnerTestResult::where('user_id', Auth::id())->first();
        
        if ($existingResult) {
            $this->isCompleted = true;
            $this->results = json_decode($existingResult->results, true);
            return;
        }

        $this->initializeQuestions();
        $this->answers = array_fill(0, $this->totalQuestions, null);
    }

    private function initializeQuestions()
    {
        $this->questions = [
            // Лингвистический интеллект (0-6)
            [
                'text' => 'Я легко запоминаю стихи, цитаты и интересные факты.',
                'type' => 'linguistic'
            ],
            [
                'text' => 'Мне нравится играть в словесные игры, решать кроссворды.',
                'type' => 'linguistic'
            ],
            [
                'text' => 'Я люблю читать книги, журналы, газеты.',
                'type' => 'linguistic'
            ],
            [
                'text' => 'Мне легко выражать свои мысли в письменной форме.',
                'type' => 'linguistic'
            ],
            [
                'text' => 'Я хорошо рассказываю истории и анекдоты.',
                'type' => 'linguistic'
            ],
            [
                'text' => 'У меня богатый словарный запас.',
                'type' => 'linguistic'
            ],
            [
                'text' => 'Я люблю изучать иностранные языки.',
                'type' => 'linguistic'
            ],

            // Логико-математический интеллект (7-13)
            [
                'text' => 'Я легко решаю математические задачи в уме.',
                'type' => 'logical_mathematical'
            ],
            [
                'text' => 'Мне нравится анализировать данные и находить закономерности.',
                'type' => 'logical_mathematical'
            ],
            [
                'text' => 'Я предпочитаю логичный, пошаговый подход к решению проблем.',
                'type' => 'logical_mathematical'
            ],
            [
                'text' => 'Мне интересны компьютерные программы и алгоритмы.',
                'type' => 'logical_mathematical'
            ],
            [
                'text' => 'Я люблю проводить эксперименты и наблюдать за результатами.',
                'type' => 'logical_mathematical'
            ],
            [
                'text' => 'Мне нравится работать с числами и статистикой.',
                'type' => 'logical_mathematical'
            ],
            [
                'text' => 'Я хорошо понимаю причинно-следственные связи.',
                'type' => 'logical_mathematical'
            ],

            // Пространственный интеллект (14-20)
            [
                'text' => 'Я легко ориентируюсь в незнакомых местах.',
                'type' => 'spatial'
            ],
            [
                'text' => 'Мне нравится рисовать, создавать схемы и диаграммы.',
                'type' => 'spatial'
            ],
            [
                'text' => 'Я хорошо представляю, как будет выглядеть объект с разных сторон.',
                'type' => 'spatial'
            ],
            [
                'text' => 'Мне легко читать карты и планы.',
                'type' => 'spatial'
            ],
            [
                'text' => 'Я предпочитаю визуальные инструкции текстовым.',
                'type' => 'spatial'
            ],
            [
                'text' => 'Мне нравится фотография и видеосъемка.',
                'type' => 'spatial'
            ],
            [
                'text' => 'Я хорошо запоминаю расположение предметов.',
                'type' => 'spatial'
            ],

            // Музыкальный интеллект (21-27)
            [
                'text' => 'Я легко запоминаю мелодии.',
                'type' => 'musical'
            ],
            [
                'text' => 'Мне нравится петь или играть на музыкальных инструментах.',
                'type' => 'musical'
            ],
            [
                'text' => 'Я чувствую ритм и могу двигаться в такт музыке.',
                'type' => 'musical'
            ],
            [
                'text' => 'Музыка помогает мне сосредоточиться или расслабиться.',
                'type' => 'musical'
            ],
            [
                'text' => 'Я замечаю, когда кто-то фальшиво поет или играет.',
                'type' => 'musical'
            ],
            [
                'text' => 'Мне нравится сочинять музыку или стихи.',
                'type' => 'musical'
            ],
            [
                'text' => 'Я часто напеваю или насвистываю мелодии.',
                'type' => 'musical'
            ],

            // Телесно-кинестетический интеллект (28-34)
            [
                'text' => 'Мне нравятся физические упражнения и спорт.',
                'type' => 'bodily_kinesthetic'
            ],
            [
                'text' => 'Я лучше учусь, когда могу двигаться или что-то делать руками.',
                'type' => 'bodily_kinesthetic'
            ],
            [
                'text' => 'У меня хорошая координация движений.',
                'type' => 'bodily_kinesthetic'
            ],
            [
                'text' => 'Мне нравится работать с инструментами или заниматься рукоделием.',
                'type' => 'bodily_kinesthetic'
            ],
            [
                'text' => 'Я предпочитаю активный отдых пассивному.',
                'type' => 'bodily_kinesthetic'
            ],
            [
                'text' => 'Мне легко изучать новые танцевальные движения.',
                'type' => 'bodily_kinesthetic'
            ],
            [
                'text' => 'Я часто жестикулирую, когда говорю.',
                'type' => 'bodily_kinesthetic'
            ],

            // Внутриличностный интеллект (35-41)
            [
                'text' => 'Я хорошо понимаю свои эмоции и настроения.',
                'type' => 'intrapersonal'
            ],
            [
                'text' => 'Мне нравится размышлять о жизни и философских вопросах.',
                'type' => 'intrapersonal'
            ],
            [
                'text' => 'Я предпочитаю работать самостоятельно.',
                'type' => 'intrapersonal'
            ],
            [
                'text' => 'У меня есть четкие цели и планы на будущее.',
                'type' => 'intrapersonal'
            ],
            [
                'text' => 'Я веду дневник или записываю свои мысли.',
                'type' => 'intrapersonal'
            ],
            [
                'text' => 'Мне нравится анализировать свое поведение и мотивы.',
                'type' => 'intrapersonal'
            ],
            [
                'text' => 'Я хорошо знаю свои сильные и слабые стороны.',
                'type' => 'intrapersonal'
            ],

            // Межличностный интеллект (42-48)
            [
                'text' => 'Я легко нахожу общий язык с разными людьми.',
                'type' => 'interpersonal'
            ],
            [
                'text' => 'Мне нравится работать в команде.',
                'type' => 'interpersonal'
            ],
            [
                'text' => 'Я хорошо чувствую настроение других людей.',
                'type' => 'interpersonal'
            ],
            [
                'text' => 'Друзья часто обращаются ко мне за советом.',
                'type' => 'interpersonal'
            ],
            [
                'text' => 'Мне нравится помогать решать конфликты между людьми.',
                'type' => 'interpersonal'
            ],
            [
                'text' => 'Я предпочитаю групповые занятия индивидуальным.',
                'type' => 'interpersonal'
            ],
            [
                'text' => 'Мне легко выступать перед аудиторией.',
                'type' => 'interpersonal'
            ],

            // Натуралистический интеллект (49-55)
            [
                'text' => 'Мне нравится наблюдать за животными и растениями.',
                'type' => 'naturalistic'
            ],
            [
                'text' => 'Я легко различаю виды растений, птиц или других живых существ.',
                'type' => 'naturalistic'
            ],
            [
                'text' => 'Мне нравится проводить время на природе.',
                'type' => 'naturalistic'
            ],
            [
                'text' => 'Я интересуюсь экологическими проблемами.',
                'type' => 'naturalistic'
            ],
            [
                'text' => 'Мне нравится классифицировать и организовывать предметы.',
                'type' => 'naturalistic'
            ],
            [
                'text' => 'Я замечаю изменения в окружающей среде.',
                'type' => 'naturalistic'
            ],
            [
                'text' => 'Мне нравится садоводство или уход за домашними животными.',
                'type' => 'naturalistic'
            ],
        ];
    }

    public function selectAnswer($value)
    {
        $this->answers[$this->currentQuestion] = $value;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestion < $this->totalQuestions - 1) {
            $this->currentQuestion++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestion > 0) {
            $this->currentQuestion--;
        }
    }

    public function submitTest()
    {
        // Проверяем, что все вопросы отвечены
        if (in_array(null, $this->answers)) {
            session()->flash('error', 'Пожалуйста, ответьте на все вопросы.');
            return;
        }

        // Подсчитываем результаты по типам интеллекта
        $scores = [
            'linguistic' => 0,
            'logical_mathematical' => 0,
            'spatial' => 0,
            'musical' => 0,
            'bodily_kinesthetic' => 0,
            'intrapersonal' => 0,
            'interpersonal' => 0,
            'naturalistic' => 0,
        ];

        foreach ($this->questions as $index => $question) {
            $scores[$question['type']] += $this->answers[$index];
        }

        // Сохраняем результаты в базу данных
        GardnerTestResult::create([
            'user_id' => Auth::id(),
            'answers' => json_encode($this->answers),
            'results' => json_encode($scores),
        ]);

        $this->isCompleted = true;
        $this->results = $scores;

        session()->flash('success', 'Тест успешно завершен!');
    }

    public function retakeTest()
    {
        // Удаляем старые результаты
        GardnerTestResult::where('user_id', Auth::id())->delete();
        
        $this->isCompleted = false;
        $this->currentQuestion = 0;
        $this->answers = array_fill(0, $this->totalQuestions, null);
        $this->results = [];
    }

    public function getIntelligenceTypes()
    {
        return [
            'linguistic' => 'Лингвистический интеллект',
            'logical_mathematical' => 'Логико-математический интеллект',
            'spatial' => 'Пространственный интеллект',
            'musical' => 'Музыкальный интеллект',
            'bodily_kinesthetic' => 'Телесно-кинестетический интеллект',
            'intrapersonal' => 'Внутриличностный интеллект',
            'interpersonal' => 'Межличностный интеллект',
            'naturalistic' => 'Натуралистический интеллект',
        ];
    }

    public function render()
    {
        return view('livewire.gardner-test')->layout('layouts.app');
    }
} 