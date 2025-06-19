<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GardnerTestResult;
use Illuminate\Support\Facades\Auth;

class GardnerTestAllQuestions extends Component
{
    public $answers = [];
    public $questions = [];
    public $totalQuestions = 44;
    public $isCompleted = false;
    public $results = [];

    public function mount()
    {
        $this->initializeQuestions();
        
        // Убеждаемся, что totalQuestions соответствует реальному количеству вопросов
        $this->totalQuestions = count($this->questions);
        
        logger()->debug('GardnerTestAllQuestions initialization', [
            'questions_count' => count($this->questions),
            'totalQuestions' => $this->totalQuestions
        ]);
        
        // Проверяем, есть ли уже результаты теста для пользователя
        $existingResult = GardnerTestResult::where('user_id', Auth::id())->first();
        
        if ($existingResult) {
            $this->isCompleted = true;
            $this->results = $existingResult->results;
            return;
        }

        $this->answers = array_fill(0, $this->totalQuestions, null);
        
        logger()->debug('Answers array initialized', [
            'answers_count' => count($this->answers),
            'answers_keys' => array_keys($this->answers)
        ]);
    }

    private function initializeQuestions()
    {
        $this->questions = [
            ['text' => 'У меня всегда были отличные оценки по математике и точным наукам', 'type' => 'logical_mathematical'],
            ['text' => 'При знакомстве с новыми людьми я умею производить хорошее впечатление', 'type' => 'interpersonal'],
            ['text' => 'Мне нравятся разные виды спорта', 'type' => 'bodily_kinesthetic'],
            ['text' => 'Я много думаю об эмоциях других людей', 'type' => 'interpersonal'],
            ['text' => 'Я часто напеваю песню или мелодию в своей голове', 'type' => 'musical'],
            ['text' => 'У меня хорошо получается сглаживать конфликты между другими людьми', 'type' => 'interpersonal'],
            ['text' => 'Мне нравятся языки и общественные науки', 'type' => 'linguistic'],
            ['text' => 'Меня интересуют такие вопросы, как «Куда движется человечество?» или «Почему мы здесь?»', 'type' => 'existential'],
            ['text' => 'Я часто размышляю о смысле жизни', 'type' => 'existential'],
            ['text' => 'Я много размышляю над собственной реакцией на определенные вещи', 'type' => 'intrapersonal'],
            ['text' => 'Мне нравится погружаться глубоко в свои мысли и анализировать все, что происходит со мной', 'type' => 'intrapersonal'],
            ['text' => 'Я хорошо лажу с животными', 'type' => 'naturalistic'],
            ['text' => 'Я не против испачкать руки, создавая, строя или ремонтируя что-то', 'type' => 'bodily_kinesthetic'],
            ['text' => 'Я часто задумываюсь над глубокими вещами, которые другим кажутся бессмысленными', 'type' => 'existential'],
            ['text' => 'Мне нравятся игры и задачи, требующие латерального мышления, например шахматы', 'type' => 'logical_mathematical'],
            ['text' => 'Я пишу стихи, цитаты, истории или веду дневник', 'type' => 'linguistic'],
            ['text' => 'Я хорошо читаю карты и ориентируюсь в незнакомой местности', 'type' => 'spatial'],
            ['text' => 'Я чувствую себя наиболее живым(-ой) в контакте с природой', 'type' => 'naturalistic'],
            ['text' => 'Я всегда открываю для себя новые виды музыки', 'type' => 'musical'],
            ['text' => 'Я часто ищу значения слов в словарях', 'type' => 'linguistic'],
            ['text' => 'Я четко помню детали декора и мебель в комнатах, в которых я когда-либо был(-а)', 'type' => 'spatial'],
            ['text' => 'Я люблю долгие прогулки на природе в одиночестве или с друзьями', 'type' => 'naturalistic'],
            ['text' => 'Я люблю проводить время наедине с собой, обдумывая свои реакции и эмоции', 'type' => 'intrapersonal'],
            ['text' => 'Мне нравится ухаживать за садами и растениями', 'type' => 'naturalistic'],
            ['text' => 'Я хорошо распознаю ложь', 'type' => 'interpersonal'],
            ['text' => 'Мне нравится читать', 'type' => 'linguistic'],
            ['text' => 'Мне нравится учить новые слова и языки', 'type' => 'linguistic'],
            ['text' => 'Я часто долго анализирую свои чувства и эмоции', 'type' => 'intrapersonal'],
            ['text' => 'Я умею работать с цифрами', 'type' => 'logical_mathematical'],
            ['text' => 'Мне нравится узнавать о том, как различные мировые религии пытались ответить на "важные вопросы человечества"', 'type' => 'existential'],
            ['text' => 'Я всегда рисую себе графики и таблицы, чтобы лучше запомнить информацию', 'type' => 'spatial'],
            ['text' => 'Мне нравится шитьё, резьба, изготовление моделей или иные виды деятельности, предусматривающие мелкую моторику', 'type' => 'bodily_kinesthetic'],
            ['text' => 'Мне легче найти решение проблемы, когда я нахожусь в движении', 'type' => 'bodily_kinesthetic'],
            ['text' => 'Мне легко понять, что я чувствую и почему', 'type' => 'intrapersonal'],
            ['text' => 'Я люблю собирать кубика Рубика и разгадывать судоку', 'type' => 'logical_mathematical'],
            ['text' => 'Мне нравится изучать различные виды растений и животных', 'type' => 'naturalistic'],
            ['text' => 'Я люблю танцы, спорт и тренировки', 'type' => 'bodily_kinesthetic'],
            ['text' => 'Я могу легко распознать фальшивую ноту', 'type' => 'musical'],
            ['text' => 'Я люблю петь или играть на музыкальных инструментах', 'type' => 'musical'],
            ['text' => 'Я лучше запоминаю лица, чем имена', 'type' => 'spatial'],
            ['text' => 'Другие люди часто приходят ко мне за поддержкой или советом', 'type' => 'interpersonal'],
            ['text' => 'Мне нравится что-то измерять или сортировать', 'type' => 'logical_mathematical'],
            ['text' => 'Я часто размышляю над философскими или теологическими вопросами', 'type' => 'existential'],
            ['text' => 'Музыка — это одно из моих самых больших увлечений', 'type' => 'musical'],
            ['text' => 'Мне легче учиться, если материал сопровождается диаграммами, схемами или иными техническими иллюстрациями', 'type' => 'spatial'],
        ];
    }

    public function selectAnswerByIndex($index, $value)
    {
        // Проверяем, что индекс находится в допустимом диапазоне
        if ($index >= 0 && $index < count($this->questions)) {
            $this->answers[$index] = $value;
        }
    }

    public function submitTest()
    {
        try {
            logger()->debug('Gardner test submission started (all questions)', [
                'user_id' => Auth::id(),
                'answers_count' => count($this->answers),
            ]);

            // Проверяем, что все вопросы отвечены
            if (in_array(null, $this->answers)) {
                logger()->debug('Some questions not answered', [
                    'null_count' => count(array_filter($this->answers, fn($x) => $x === null))
                ]);
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
                'existential' => 0,
            ];

            foreach ($this->questions as $index => $question) {
                if (isset($this->answers[$index])) {
                    $scores[$question['type']] += $this->answers[$index];
                }
            }

            // Русские названия типов интеллекта
            $intelligenceTypes = [
                'linguistic' => 'Лингвистический интеллект',
                'logical_mathematical' => 'Логико-математический интеллект',
                'spatial' => 'Пространственный интеллект',
                'musical' => 'Музыкальный интеллект',
                'bodily_kinesthetic' => 'Телесно-кинестетический интеллект',
                'intrapersonal' => 'Внутриличностный интеллект',
                'interpersonal' => 'Межличностный интеллект',
                'naturalistic' => 'Натуралистический интеллект',
                'existential' => 'Экзистенциальный интеллект',
            ];

            // Преобразуем баллы в проценты и создаем результаты с русскими названиями
            $results = [];
            foreach ($scores as $type => $score) {
                $percentage = round(($score / 25) * 100, 0); // Максимум 25 баллов = 100% (5 вопросов × 5 баллов)
                $results[$intelligenceTypes[$type]] = $percentage . '%';
            }

            logger()->debug('Scores calculated', ['scores' => $scores, 'results' => $results]);

            // Сохраняем результаты в базу данных
            $result = GardnerTestResult::create([
                'user_id' => Auth::id(),
                'answers' => $this->answers,
                'results' => $results,
            ]);

            logger()->debug('Gardner test result saved', ['result_id' => $result->id]);

            $this->isCompleted = true;
            $this->results = $results;

            session()->flash('success', 'Тест успешно завершен!');
            
        } catch (\Exception $e) {
            logger()->error('Error submitting Gardner test', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Произошла ошибка при сохранении результатов: ' . $e->getMessage());
        }
    }

    public function retakeTest()
    {
        // Удаляем старые результаты
        GardnerTestResult::where('user_id', Auth::id())->delete();
        
        $this->isCompleted = false;
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
            'existential' => 'Экзистенциальный интеллект',
        ];
    }

    public function render()
    {
        return view('livewire.gardner-test-all-questions')->layout('layouts.app');
    }
} 